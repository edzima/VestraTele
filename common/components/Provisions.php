<?php

namespace common\components;

use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\provision\Provision;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\Worker;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Provisions extends Component {

	/** @var ProvisionType[] */
	private $types;

	/**
	 * @param bool $refresh
	 * @return ProvisionType[]
	 */
	public function getTypes(bool $refresh = false): array {
		if (empty($this->types) || $refresh) {
			$this->types = ProvisionType::find()
				->indexBy('id')
				->all();
		}
		return $this->types;
	}

	public function removeForIssue(Issue $issue): void {
		Provision::deleteAll(['pay_id' => ArrayHelper::getColumn($issue->pays, 'id')]);
	}

	public function hasAllProvisions(Worker $user): bool {
		$typesIds = $this->getTypesIdsForUser($user->id);
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
	public function getIssueUsersProvisions(Issue $issue): array {
		$provisions = $this->issueProvisions($issue);
		return [
			'agent' => $this->userFilter($this->roleFilter($provisions, Worker::ROLE_AGENT), $issue->agent_id),
			'lawyer' => $this->userFilter($this->roleFilter($provisions, Worker::ROLE_LAWYER), $issue->lawyer_id),
			'tele' => ($issue->hasTele() ? $this->userFilter($this->roleFilter($provisions, Worker::ROLE_TELEMARKETER), $issue->tele_id) : []),
		];
	}

	/**
	 * @param Worker $user
	 * @param int $typeId
	 * @param IssuePay[] $pays
	 * @return int
	 */
	public function add(Worker $user, int $typeId, array $pays): int {
		$usersProvision = ProvisionUser::find()
			->andWhere(['from_user_id' => $user->id])
			->andWhere(['type_id' => $typeId])
			->with('type')
			->all();
		$provisions = [];
		foreach ($pays as $pay) {
			foreach ($usersProvision as $provisionUser) {
				$brutto = $this->calculateProvision($provisionUser, $pay->value);
				$value = Yii::$app->tax->netto($brutto, $pay->vat);
				if ($value > 0) {
					$provisions[] = [
						'pay_id' => $pay->id,
						'to_user_id' => $provisionUser->to_user_id,
						'from_user_id' => $provisionUser->from_user_id,
						'value' => $value,
						'type_id' => $typeId,
					];
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

	private function calculateProvision(ProvisionUser $provisionUser, float $value): float {
		if ($provisionUser->type->is_percentage) {
			return $value * $provisionUser->value / 100;
		}
		return $provisionUser->value;
	}

	public function isValidForIssue(Issue $issue, ProvisionUser $provisionUser): bool {
		return empty($provisionUser->type->getTypesIds())
			|| in_array($issue->type_id, $provisionUser->type->getTypesIds());
	}

	public function isTypeForUser(ProvisionType $type, int $userId): bool {
		return in_array($type->id, $this->getTypesIdsForUser($userId));
	}

	public function isTypeForRole(ProvisionType $type, string $role): bool {
		return empty($type->getRoles()) || in_array($role, $type->getRoles());
	}

	private function getTypesIdsForUser(int $userId): array {
		$ids = [];
		foreach ($this->getTypes() as $type) {
			if ($type->isValidForUser($userId)) {
				$ids[] = $type->id;
			}
		}
		return $ids;
	}

	private function issueProvisions(Issue $issue): array {
		$models = ProvisionUser::find()
			->with('type')
			->joinWith('type T')
			->andWhere([
				'from_user_id' => [
					$issue->agent_id,
					$issue->tele_id,
					$issue->lawyer_id,
				],
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
