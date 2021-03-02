<?php

namespace common\components;

use backend\modules\provision\models\ProvisionUserData;
use common\models\issue\IssuePay;
use common\models\provision\Provision;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\settlement\PayInterface;
use common\models\user\Worker;
use Decimal\Decimal;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

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

	public function getIssueUserTypes(string $userType): array {
		$ids = [];
		foreach ($this->getTypes() as $type) {
			if ($type->isForIssueUser($userType)) {
				$ids[$type->id] = $type;
			}
		}
		return $ids;
	}

	public function issuePayValue(IssuePay $pay): Decimal {
		return $pay->getValueWithoutVAT()->sub($pay->getCosts(false));
	}

	/**
	 * @param ProvisionUserData $userData
	 * @param Decimal[] $pays indexed by pay id.
	 * @throws InvalidConfigException
	 */
	public function addFromUserData(ProvisionUserData $userData, array $pays): int {
		if (!$userData->type) {
			throw new InvalidConfigException('Type for userData must be set before add provisions.');
		}
		$type = $userData->type;
		$provisions = [];

		$selfModels = $userData->getSelfQuery()->all();
		foreach ($selfModels as $model) {
			foreach ($pays as $payId => $payValue) {
				$value = $this->calculateProvision($model, $payValue);
				$provisions[] = [
					'pay_id' => $payId,
					'to_user_id' => $model->to_user_id,
					'from_user_id' => $model->from_user_id,
					'value' => $value->toFixed(2),
					'type_id' => $model->type_id,
				];
			}
		}
		if ($type->getWithHierarchy()) {
			$toModels = $userData->getToQuery()->all();
			foreach ($toModels as $model) {
				foreach ($pays as $payId => $payValue) {
					$value = $this->calculateProvision($model, $payValue);
					$provisions[] = [
						'pay_id' => $payId,
						'to_user_id' => $model->to_user_id,
						'from_user_id' => $model->from_user_id,
						'value' => $value->toFixed(2),
						'type_id' => $model->type_id,
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

	public function getValueToProvision(PayInterface $pay): Decimal {
		return $pay->getValue();
	}

	public function generateProvision(ProvisionUser $provisionUser, PayInterface $pay): Provision {
		return new Provision($this->generateProvisionData($provisionUser, $pay));
	}

	protected function generateProvisionData(ProvisionUser $provisionUser, ProvisionPay $pay): array {
		$value = $this->calculateProvision($provisionUser, $pay->getProvisionValue());
		return [
			'pay_id' => $pay->id,
			'to_user_id' => $provisionUser->to_user_id,
			'from_user_id' => $provisionUser->from_user_id,
			'value' => $value->toFixed(2),
			'type_id' => $provisionUser->type_id,
		];
	}

	/**
	 * @param int $userId
	 * @param int $typeId
	 * @param IssuePay[] $pays
	 * @return int
	 */
	public function add(int $userId, int $typeId, array $pays): int {
		$usersProvision = ProvisionUser::find()
			->onlyFrom($userId)
			->forType($typeId)
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

	public function calculateProvision(ProvisionUser $provisionUser, Decimal $value): Decimal {
		if ($provisionUser->type->is_percentage) {
			return $value->mul($provisionUser->getValue())->div(100);
		}
		return $provisionUser->getValue();
	}

}
