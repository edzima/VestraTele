<?php

namespace common\components\provision;

use common\components\provision\exception\MissingParentProvisionUserException;
use common\components\provision\exception\MissingProvisionUserException;
use common\components\provision\exception\MissingSelfProvisionUserException;
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
use yii\base\Model;

class Provisions extends Component {

	protected array $batchColumns = [
		'pay_id',
		'value',
		'to_user_id',
		'from_user_id',
		'type_id',
		'percent',
	];

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
	 * @throws MissingProvisionUserException
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
				, 'provision.settlement'
			);
			return 0;
		}
		$forms = SettlementUserProvisionsForm::createModels($model, $userTypes, $config);
		$provisions = [];
		foreach ($forms as $form) {
			$typesCount = count($form->getTypes());
			$form->linkIssueNotSettledUserCosts();
			if ($typesCount === 1) {
				$form->typeId = array_key_first($form->getTypes());
				$provisions[] = $this->generateProvisionsData($form->getData(), $form->getPaysValues());
			}

			if ($typesCount > 1) {
				Yii::warning([
					'message' => Yii::t('provision', 'Settlement: {type} has more than one active provision type for user type: {userType}.', [
						'type' => $form->getModel()->getTypeName(),
						'userType' => $form->getIssueUser()->getTypeName(),
					]),
					'settlement' => [
						'id' => $model->getId(),
						'issueId' => $model->getIssueId(),
						'type' => $model->getTypeName(),
					],
					'types' => $form->getTypesNames(),
				], 'provision.settlement');
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
				'type' => $userData->type->getNameWithTypeName(),
			]);

			Yii::warning($message, 'provision.user.self');
			throw new MissingSelfProvisionUserException($message);
		}
		foreach ($selfModels as $model) {
			foreach ($pays as $payId => $payValue) {
				$provisions[] = $this->generateData($payId, $model, $payValue);
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

					Yii::warning($message, 'provision.user.parents');
					throw new MissingParentProvisionUserException($message);
				}
			}

			$toModels = $userData->getToQuery()->all();
			foreach ($toModels as $model) {
				foreach ($pays as $payId => $payValue) {
					$provisions[] = $this->generateData($payId, $model, $payValue);
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

	protected function generateData(int $pay_id, ProvisionUser $provisionUser, Decimal $baseValue = null): array {
		return [
			'pay_id' => $pay_id,
			'value' => $provisionUser->generateProvision($baseValue)->toFixed(2),
			'to_user_id' => $provisionUser->to_user_id,
			'from_user_id' => $provisionUser->from_user_id,
			'type_id' => $provisionUser->type_id,
			'percent' => $provisionUser->type->is_percentage ? $provisionUser->value : null,
		];
	}

}
