<?php

namespace common\components\provision;

use common\components\provision\exception\MissingParentProvisionUserException;
use common\components\provision\exception\MissingProvisionUserException;
use common\components\provision\exception\MissingSelfProvisionUserException;
use common\components\provision\exception\MultipleSettlementProvisionTypesException;
use common\models\issue\IssuePay;
use common\models\issue\IssueSettlement;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserData;
use common\models\provision\SettlementUserProvisionsForm;
use Decimal\Decimal;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Provisions extends Component {

	protected array $batchColumns = [
		'pay_id',
		'value',
		'to_user_id',
		'from_user_id',
		'type_id',
		'percent',
	];

	/**
	 * @param IssuePay[] $pays
	 * @param int|null $to_user_id
	 * @return Decimal
	 */
	public function sumProvision(array $pays, int $to_user_id = null): Decimal {
		$sum = new Decimal(0);
		foreach ($pays as $pay) {
			$provisions = $pay->provisions;
			foreach ($provisions as $provision) {
				if ($to_user_id === null || $provision->to_user_id === $to_user_id) {
					$sum = $sum->add($provision->getValue());
				}
			}
		}
		return $sum;
	}

	public function removeForPays(array $ids): int {
		if (!empty($ids)) {
			return Provision::deleteAll(['pay_id' => $ids]);
		}
		return 0;
	}

	/**
	 * @param IssueSettlement $model
	 * @param array $userTypes
	 * @param array $config
	 * @return int
	 * @throws InvalidConfigException
	 * @throws MissingProvisionUserException|MultipleSettlementProvisionTypesException
	 * @todo maybe add $throwException param.
	 */
	public function settlement(IssueSettlement $model, array $userTypes = [], array $config = []): int {
		if (empty(IssueProvisionType::findSettlementTypes($model))) {
			Yii::warning(
				[
					'message' => Yii::t('provision', 'Not found active type for settlement: {type}', [
						'type' => $model->getTypeName(),
					]),
					'settlement_id' => $model->id,
					'issue_id' => $model->issue_id,
				]
				, __METHOD__
			);
			return 0;
		}
		$forms = SettlementUserProvisionsForm::createModels($model, $userTypes, $config);
		$provisions = [];
		foreach ($forms as $form) {
			$types = $form->getTypes();
			$typesCount = count($types);
			$form->linkIssueNotSettledUserCosts();
			if ($typesCount > 1) {
				$withBaseTypes = array_filter($types, static function (IssueProvisionType $type) {
					return $type->getBaseTypeId() !== null;
				});
				if (count($withBaseTypes) > 1) {
					$message = Yii::t('provision', 'Settlement: {type} has more than one active provision type for user type: {userType}.', [
						'type' => $form->getModel()->getTypeName(),
						'userType' => $form->getIssueUser()->getTypeName(),
					]);
					Yii::warning([
						'message' => $message,
						'settlement' => [
							'id' => $model->getId(),
							'issueId' => $model->getIssueId(),
							'type' => $model->getTypeName(),
						],
						'types' => $form->getTypesNames(),
					], __METHOD__);
					throw new MultipleSettlementProvisionTypesException($message);
				}
			}
			if ($typesCount === 1) {
				$type = reset($types);
				Yii::debug(
					Yii::t('provision', 'Found Active Type for Settlement: {settlementType} for: {userWithType}.', [
						'settlementType' => $form->getModel()->getTypeName(),
						'userWithType' => $form->getIssueUser()->getTypeWithUser(),
					]), __METHOD__);
				$form->typeId = $type->id;
				$provisions[] = $this->generateProvisionsData($form->getData(), $form->getPaysValues(), $model->type->is_percentage);
			} else {
				Yii::warning([
					'msg' => 'Types count: ' . $typesCount,
					'settlementType' => $form->getModel()->getTypeName(),
					'userWithType' => $form->getIssueUser()->getTypeWithUser(),
				], __METHOD__);
			}
		}

		$count = 0;
		foreach ($provisions as $provisionData) {
			$count += $this->batchInsert($provisionData);
		}
		return $count;
	}

	public function issuePayValue(IssuePay $pay): Decimal {
		return $pay->getValueWithoutVAT();
	}

	/**
	 * @param ProvisionUserData $userData
	 * @param Decimal[] $pays indexed by pay id.
	 * @throws InvalidConfigException|MissingProvisionUserException
	 */
	public function generateProvisionsData(ProvisionUserData $userData, array $pays, bool $mul = false): array {
		if (!$userData->type) {
			throw new InvalidConfigException('Type for userData must be set before generate provisions.');
		}
		Yii::debug(
			Yii::t('provision', 'Generate Provision: {type} for user: {user} with date: {date}', [
				'type' => $userData->type->name,
				'user' => $userData->getUser()->getFullName(),
				'date' => Yii::$app->formatter->asDate($userData->date),
			]), __METHOD__);
		$type = $userData->type;
		$baseType = $type->getBaseType();
		$provisions = [];
		if ($baseType !== null) {
			$userData->type = $baseType;
		}
		$selfModels = $userData->getSelfQuery()->all();

		if (empty($selfModels)) {
			$message = Yii::t('provision', '{user} has not set self provision for type: {type}', [
				'user' => $userData->getUser()->getFullName(),
				'type' => $userData->type->getNameWithTypeName(),
			]);

			Yii::warning($message, __METHOD__);
			throw new MissingSelfProvisionUserException($message);
		}
		foreach ($selfModels as $model) {
			if ($baseType !== null) {
				$model = ProvisionUser::createFromBaseType($model, $type);
			}
			foreach ($pays as $payId => $payValue) {
				$provisions[] = $this->generateData(
					$payId,
					$model,
					$payValue,
					$mul
				);
			}
		}
		if ($type->getWithHierarchy()) {
			$parentsWithoutProvisionsQuery = $userData->getAllParentsQueryWithoutProvision();
			if ($parentsWithoutProvisionsQuery) {
				$parentsWithoutProvisionsCount = $parentsWithoutProvisionsQuery->count();
				if ($parentsWithoutProvisionsCount > 0) {
					$message = Yii::t('provision', '{user} has not setted parents ({count}) provision for type: {type}', [
						'user' => $userData->getUser()->getFullName(),
						'type' => $userData->type->getNameWithTypeName(),
						'count' => $parentsWithoutProvisionsCount,
					]);

					Yii::warning($message, __METHOD__);
					throw new MissingParentProvisionUserException($message);
				}
			}

			$toModels = $userData->getToQuery()->all();
			foreach ($toModels as $model) {
				if ($baseType !== null) {
					$model = ProvisionUser::createFromBaseType($model, $type);
				}
				foreach ($pays as $payId => $payValue) {
					$provisions[] = $this->generateData($payId, $model, $payValue, $mul);
				}
			}
		}

		return $provisions;
	}

	public function batchInsert(array $provisions, array $columns = []): int {
		if (empty($provisions)) {
			return 0;
		}
		if (empty($columns)) {
			$columns = $this->batchColumns;
		}
		return Yii::$app->db->createCommand()
			->batchInsert(Provision::tableName(), $columns, $provisions)
			->execute();
	}

	protected function generateData(int $pay_id, ProvisionUser $provisionUser, Decimal $baseValue = null, bool $mul = false): array {
		$type = $provisionUser->type;
		$value = $provisionUser->generateProvision($baseValue, $mul);
		$percent = null;
		if ($type->is_percentage && $baseValue !== null) {
			$percent = $value->div($baseValue)->mul(100);
		}
		return [
			'pay_id' => $pay_id,
			'value' => $value->toFixed(2),
			'to_user_id' => $provisionUser->to_user_id,
			'from_user_id' => $provisionUser->from_user_id,
			'type_id' => $type->id,
			'percent' => $percent ? $percent->toFixed(2) : null,
		];
	}

}
