<?php

namespace common\components\provision;

use common\components\provision\exception\MissingParentProvisionUserException;
use common\components\provision\exception\MissingProvisionUserException;
use common\components\provision\exception\MissingSelfProvisionUserException;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use common\models\provision\ProvisionUserData;
use common\models\provision\SettlementUserProvisionsForm;
use Decimal\Decimal;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Provisions extends Component {

	public function removeForPays(array $ids): int {
		if (!empty($ids)) {
			return Provision::deleteAll(['pay_id' => $ids]);
		}
		return 0;
	}

	/**
	 * @param IssuePayCalculation $model
	 * @param array $userTypes
	 * @param array $config
	 * @return int
	 * @throws InvalidConfigException
	 * @throws MissingProvisionUserException
	 */
	public function settlement(IssuePayCalculation $model, array $userTypes = [], array $config = []): int {
		if (empty(IssueProvisionType::findCalculationTypes($model))) {
			Yii::warning(
				[
					'message' => Yii::t('provision', 'Not found active type for settlement: {type}', [
						'type' => $model->getTypeName(),
					]),
					'settlement_id' => $model->id,
					'issue_id' => $model->issue_id,
				]
			);
			return 0;
		}
		$forms = SettlementUserProvisionsForm::createModels($model, $userTypes, $config);
		$provisions = [];
		foreach ($forms as $form) {
			$typesCount = count($form->getTypes());

			/*
			if ($typesCount === 0) {
				Yii::warning([
					'message' => Yii::t('provision', 'Settlement: {type} has not active provisions type for user type: {userType}',
						[
							'type' => $form->getModel()->getTypeName(),
							'userType' => $form->getIssueUser()->getTypeName(),
						]),
					'settlement' => $model->attributes,
				], 'provision.settlement.generate');

				continue;
			}
*/
			if ($typesCount === 1) {
				$form->typeId = array_key_first($form->getTypes());
				$provisions[] = $this->generateProvisionsData($form->getData(), $form->getPaysValues());
			}

			if ($typesCount > 1) {
				Yii::warning([
					'message' => Yii::t('provision', 'Settlement: {type} has more than one active provision type for user type: {userType}', [
						'type' => $form->getModel()->getTypeName(),
						'userType' => $form->getIssueUser()->getTypeName(),
					]),
					'settlement' => $model->attributes,
					'types' => $form->getTypesNames(),
				], 'provision.settlement');
			}
		}

		return $this->batchInsert(array_values($provisions));
	}

	public function issuePayValue(IssuePay $pay): Decimal {
		//@todo remove only general costs, move to issue pay?
		return $pay->getValueWithoutVAT()
			->sub($pay->getCosts(false));
	}

	/**
	 * @param ProvisionUserData $userData
	 * @param Decimal[] $pays indexed by pay id.
	 * @throws InvalidConfigException|MissingProvisionUserException
	 */
	public function generateProvisionsData(ProvisionUserData $userData, array $pays): array {
		if (!$userData->type) {
			throw new InvalidConfigException('Type for userData must be set before generate provisions.');
		}
		$type = $userData->type;
		$provisions = [];

		$selfModels = $userData->getSelfQuery()->all();

		if (empty($selfModels)) {
			$message = Yii::t('provision', '{user} has not set self provision for type: {type}', [
				'user' => $userData->getUser()->getFullName(),
				'type' => $userData->type->getNameWithValue(),
			]);

			Yii::warning($message, 'provision.user.self');
			throw new MissingSelfProvisionUserException($message);
		}
		foreach ($selfModels as $model) {
			foreach ($pays as $payId => $payValue) {
				$provisions[] = [
					'pay_id' => $payId,
					'value' => $model->generateProvision($payValue)->toFixed(2),
					'to_user_id' => $model->to_user_id,
					'from_user_id' => $model->from_user_id,
					'type_id' => $model->type_id,
				];
			}
		}
		if ($type->getWithHierarchy()) {
			$parentsWithoutProvisionsQuery = $userData->getAllParentsQueryWithoutProvision();
			if ($parentsWithoutProvisionsQuery) {
				$parentsWithoutProvisionsCount = $parentsWithoutProvisionsQuery->count();
				if ($parentsWithoutProvisionsCount > 0) {
					$message = Yii::t('provision', '{user} has not setted parents ({count}) provision for type: {type}', [
						'user' => $userData->getUser()->getFullName(),
						'type' => $userData->type->getNameWithValue(),
						'count' => $parentsWithoutProvisionsCount,
					]);

					Yii::warning($message, 'provision.user.parents');
					throw new MissingParentProvisionUserException($message);
				}
			}

			$toModels = $userData->getToQuery()->all();
			foreach ($toModels as $model) {
				foreach ($pays as $payId => $payValue) {
					$provisions[] = [
						'pay_id' => $payId,
						'value' => $model->generateProvision($payValue)->toFixed(2),
						'to_user_id' => $model->to_user_id,
						'from_user_id' => $model->from_user_id,
						'type_id' => $model->type_id,
					];
				}
			}
		}

		return $provisions;
	}

	public function batchInsert(array $provisions, $columns = [
		'pay_id',
		'value',
		'to_user_id',
		'from_user_id',
		'type_id',
	]
	): int {
		if (empty($provisions)) {
			return 0;
		}
		return Yii::$app->db->createCommand()
			->batchInsert(Provision::tableName(), [
				$columns,
			], $provisions)
			->execute();
	}

}
