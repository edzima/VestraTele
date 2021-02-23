<?php

namespace common\components;

use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\provision\Provision;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\Worker;
use Decimal\Decimal;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Provisions extends Component {

	/** @var ProvisionType[]|null */
	private ?array $types = null;

	/**
	 * @param bool $refresh
	 * @return ProvisionType[]
	 */
	public function getTypes(bool $refresh = false): array {
		if ($this->types === null || $refresh) {
			$this->types = ProvisionType::find()
				->indexBy('id')
				->all();
		}
		return $this->types;
	}

	public function removeForPays(array $ids): void {
		if (!empty($ids)) {
			Provision::deleteAll(['pay_id' => $ids]);
		}
	}

	public function hasAllProvisions(Worker $user, string $userType): bool {
		$typesIds = array_keys($this->getIssueUserTypes($userType));
		$toUserIds = $user->getParentsIds();
		$toUserIds[] = $user->id;
		$provisionsCount = ProvisionUser::find()
			->andWhere(['from_user_id' => $user->id])
			->andWhere(['to_user_id' => $toUserIds])
			->andWhere(['type_id' => $typesIds])
			->count();
		$allProvisionsCount = count($typesIds) * count($toUserIds);
		return (int) $provisionsCount === $allProvisionsCount;
	}

	/**
	 * @param Issue $issue
	 * @return ProvisionUser[][]
	 */
	public function getIssueWorkersProvisions(Issue $issue): array {
		$workers = $issue->getUsers()->onlyWorkers()
			->indexBy('type')
			->all();
		$ids = ArrayHelper::getColumn($workers, 'user_id');
		$provisions = $this->issueProvisions($issue, $ids);
		$workersProvisions = [];
		foreach ($workers as $worker) {
			$workersProvisions[$worker->type] = $this->userFilter($this->roleFilter($provisions, $worker->type), $worker->user_id);
		}
		return $workersProvisions;
	}

	public function issuePayValue(IssuePay $pay): Decimal {
		return $pay->getValueWithoutVAT()->sub($pay->getCosts(false));
	}

	/**
	 * @param int $userId
	 * @param int $typeId
	 * @param IssuePay[] $pays
	 * @return int
	 */
	public function add(int $userId, int $typeId, array $pays): int {
		$usersProvision = ProvisionUser::find()
			->andWhere(['from_user_id' => $userId])
			->andWhere(['type_id' => $typeId])
			->with('type')
			->all();
		$provisions = [];
		foreach ($pays as $pay) {
			foreach ($usersProvision as $provisionUser) {
				$value = $this->calculateProvision($provisionUser, $this->issuePayValue($pay));
				if ($value->isPositive()) {
					$provisions[] = [
						'pay_id' => $pay->id,
						'to_user_id' => $provisionUser->to_user_id,
						'from_user_id' => $provisionUser->from_user_id,
						'value' => $value->toFixed(2),
						'type_id' => $typeId,
					];
				} else {
					Yii::warning([
						'message' => 'Provision value is not positive',
						'pay_id' => $pay->id,
						'to_user_id' => $provisionUser->to_user_id,
						'from_user_id' => $provisionUser->from_user_id,
						'value' => $value->toFixed(2),
						'type_id' => $typeId,
					], 'provisions');
				}
			}
		}
		return Yii::$app->db->createCommand()
			->batchInsert(Provision::tableName(), [
				'pay_id',
				'to_user_id',
				'from_user_id',
				'value',
				'type_id',
			], $provisions)
			->execute();
	}

	private function calculateProvision(ProvisionUser $provisionUser, Decimal $value): Decimal {
		if ($provisionUser->type->is_percentage) {
			return $value->mul($provisionUser->getValue())->div(100);
		}
		return $provisionUser->getValue();
	}

	public function isValidForIssue(Issue $issue, ProvisionUser $provisionUser): bool {
		return empty($provisionUser->type->getIssueTypesIds())
			|| in_array($issue->type_id, $provisionUser->type->getIssueTypesIds());
	}

	public function isTypeForRole(ProvisionType $type, string $role): bool {
		return empty($type->getRoles()) || in_array($role, $type->getRoles());
	}

	public function getIssueUserTypes(string $userType): array {
		$ids = [];
		foreach ($this->getTypes() as $type) {
			if ($type->isForIssueUser($userType)) {
				$ids[$type->id] = $type;
			}
		}
		return $ids;
	}

	private function issueProvisions(Issue $issue, array $usersIds): array {
		$models = ProvisionUser::find()
			->with('type')
			->joinWith('type T')
			->andWhere([
				'from_user_id' => $usersIds,
			])
			->orderBy('T.only_with_tele DESC')
			->all();
		$models = $this->typeFilter($models, $issue);
		$models = $this->teleFilter($models, $issue);
		return $models;
	}

	private function typeFilter(array $provisions, Issue $issue): array {
		return array_filter($provisions, function (ProvisionUser $provisionUser) use ($issue) {
			return $this->isValidForIssue($issue, $provisionUser);
		});
	}

	private function teleFilter(array $provisions, Issue $issue): array {
		return array_filter($provisions, static function (ProvisionUser $provisionUser) use ($issue) {
			if ($provisionUser->type->only_with_tele) {
				return $issue->hasTele();
			}
			return true;
		});
	}

	private function userFilter(array $provisions, int $userId): array {
		return array_filter($provisions, static function (ProvisionUser $provision) use ($userId) {
			return $provision->to_user_id === $userId && $provision->from_user_id === $userId;
		});
	}

	private function roleFilter(array $provisions, string $role): array {
		return array_filter($provisions, function (ProvisionUser $provision) use ($role) {
			return $this->isTypeForRole($provision->type, $role);
		});
	}

}
