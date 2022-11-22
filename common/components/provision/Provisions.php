<?php

namespace common\components\provision;

use common\components\provision\exception\MissingParentProvisionUserException;
use common\components\provision\exception\MissingProvisionUserException;
use common\components\provision\exception\MissingSelfProvisionUserException;
use common\components\provision\exception\MultipleSettlementProvisionTypesException;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\IssueInterface;
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

	public function onIssueUserEvent(IssueUserEvent $event): void {
		$issue = $event->sender;
		if (!$issue instanceof IssueInterface) {
			$issue = $event->model->issue;
		}
		if (!empty($issue->getIssueModel()->payCalculations)) {
			foreach ($issue->getIssueModel()->payCalculations as $settlement) {
				if (!$settlement->isProvisionControl() && $settlement->hasProvisions()) {
					$settlement->markAsProvisionControl();
					$settlement->save(false);

					$message = Yii::t('provision', 'Change User: {user} in Settlement: {settlement} with Provision.', [
						'user' => $event->model->getTypeWithUser(),
						'settlement' => $settlement->getTypeName(),
					]);
					Yii::warning($message, 'provision.issueUserEvent');
					Yii::$app
						->mailer
						->compose(
							['html' => 'issueUserChangeForSettlementWithProvisions-html', 'text' => 'issueUserChangeForSettlementWithProvisions-text'],
							[
								'event' => $event,
								'settlement' => $settlement,
							]
						)
						->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
						->setTo(Yii::$app->params['provisionEmail'])
						->setSubject($message)
						->send();
				}
			}
			Yii::warning('Change User for Issue: ' . $issue->getIssueName() . ' who has Settlements.');
			$provisions = $issue->getIssueModel()
				->getPays()
				->joinWith('provisions p')
				->andWhere('p.id IS NOT NULL')
				->exists();
			if ($provisions) {
				//@todo dont remove provision
				//@todo send Email to Provision Manager about Settlement with provision control problem.
				//@todo create settlement note with issue details.
				Yii::warning('Issue has already Provisions.');
			}
		}
	}

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
				, 'provision.settlement'
			);
			return 0;
		}
		$forms = SettlementUserProvisionsForm::createModels($model, $userTypes, $config);
		$provisions = [];
		foreach ($forms as $form) {
			$typesCount = count($form->getTypes());
			$form->linkIssueNotSettledUserCosts();
			if ($typesCount > 1) {
				$withBaseTypes = array_filter($form->getTypes(), static function (IssueProvisionType $type) {
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
					], 'provision.settlement');
					throw new MultipleSettlementProvisionTypesException($message);
				}
			}
			if ($typesCount === 1) {
				$form->typeId = array_key_first($form->getTypes());
				$provisions[] = $this->generateProvisionsData($form->getData(), $form->getPaysValues());
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

			Yii::warning($message, 'provision.user.self');
			throw new MissingSelfProvisionUserException($message);
		}
		foreach ($selfModels as $model) {
			if ($baseType !== null) {
				$model = ProvisionUser::createFromBaseType($model, $type);
			}
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
				if ($baseType !== null) {
					$model = ProvisionUser::createFromBaseType($model, $type);
				}
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
		$type = $provisionUser->type;
		$value = $provisionUser->generateProvision($baseValue);
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
