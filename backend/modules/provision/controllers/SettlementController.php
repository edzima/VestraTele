<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use common\components\provision\exception\Exception;
use common\components\provision\exception\MissingProvisionUserException;
use common\helpers\Flash;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SettlementController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'delete-multi' => ['POST'],
					'generate' => ['POST'],
					'generate-without-provisions' => ['POST'],
				],
			],
		];
	}

	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$dataProvider = new ActiveDataProvider([
			'query' => Provision::find()
				->andWhere(['pay_id' => $model->getPays()->getIds()])
				->joinWith('pay.calculation.pays')
				->orderBy(['value' => SORT_DESC]),
			'pagination' => false,
			'sort' => false,
		]);

		$userModels = SettlementUserProvisionsForm::createModels($model);

		if (empty(IssueProvisionType::findSettlementTypes($model))) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'Not active types for settlement: {settlement}.', ['settlement' => $model->getTypeName()])
			);
		}

		Url::remember();

		return $this->render('view', [
			'model' => $model,
			'dataProvider' => $dataProvider,
			'userModels' => $userModels,
		]);
	}

	public function actionGenerate(int $id) {
		$model = $this->findModel($id);
		$this->generate($model, false);

		return $this->redirect(['view', 'id' => $id]);
	}

	public function actionGenerateMultiple(array $ids) {
		foreach ($ids as $id) {
			$this->generate($this->findModel($id), true);
		}
		return $this->redirect(['/settlement/calculation/without-provisions']);
	}

	private function generate(IssueSettlement $model, bool $withName): void {
		try {

			$name = $withName ? $model->getIssueName() . ' - ' . $model->getTypeName() : null;
			$count = Yii::$app->provisions->settlement($model);
			if ($count === 0) {
				Flash::add(Flash::TYPE_WARNING,
					Yii::t('provision',
						$name
							? 'Any user has not provision in {name}.'
							: 'Any user has not provision.'
						, ['name' => $name]
					)
				);
			}
			if ($count > 0) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('provision',
						$name
							? 'Success! Generate {count} provisions in {name}.'
							: 'Success! Generate {count} provisions.',
						['count' => $count, 'name' => $name]
					)
				);
			}
		} catch (Exception $exception) {
			Flash::add(Flash::TYPE_ERROR, $exception->getMessage());
		}
	}

	public function actionUser(int $id, string $issueUserType, int $typeId = null) {
		$settlement = $this->findModel($id);

		try {
			$model = new SettlementUserProvisionsForm($settlement, $issueUserType);
		} catch (InvalidConfigException $e) {
			throw new NotFoundHttpException($e->getMessage());
		}

		if ($typeId) {
			$type = $model->getType($typeId);
			if ($type === null) {
				throw new NotFoundHttpException();
			}
			$model->typeId = $typeId;
		} else {
			$types = $model->getTypes();
			if (!empty($types)) {
				/* @var IssueProvisionType|null $type */
				$type = reset($types);
				if ($type !== null) {
					return $this->redirect(['user', 'id' => $settlement->getId(), 'issueUserType' => $issueUserType, 'typeId' => $type->id]);
				}
			}
		}

		if (empty($model->getTypes())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision',
					'Not active {userType} types for settlement: {settlement}.', [
						'userType' => $model->getIssueUser()->getTypeName(),
						'settlement' => $settlement->getTypeName(),
					])
			);
		}

		$navTypesItems = [];
		foreach ($model->getTypes() as $type) {
			$navTypesItems[] = [
				'label' => $type->getNameWithTypeName(),
				'url' => ['user', 'id' => $settlement->getId(), 'issueUserType' => $issueUserType, 'typeId' => $type->id],
				'active' => $type->id === $typeId,
			];
		}

		Url::remember();

		$userNotSettledCosts = new ActiveDataProvider([
			'query' => IssueCost::find()
				->notSettled()
				->withoutSettlements()
				->andWhere([
					'or', [
						'user_id' => $model->getIssueUser()->user_id,
					], [
						IssueCost::tableName() . '.issue_id' => $settlement->getIssueId(),
						'user_id' => null,
					],
				]),

		]);

		$settlementCostDataProvider = new ArrayDataProvider([
				'allModels' => array_merge(
					$settlement->getCostsWithUser($model->getIssueUser()->user_id),
					$settlement->getCostsWithoutUser($model->getIssueUser()->user_id)
				),
				'modelClass' => IssueCost::class,
				'key' => 'id',
			]
		);

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			try {
				$provisions = Yii::$app->provisions->generateProvisionsData($model->getData(), $model->getPaysValues());
				$count = Yii::$app->provisions->batchInsert($provisions);
				if ($count === 0) {
					Flash::add(Flash::TYPE_WARNING,
						Yii::t('provision', 'User has not provision.'));
				}
				if ($count > 0) {
					Flash::add(Flash::TYPE_SUCCESS, Yii::t('provision', 'Success! Generate {count} provisions.', ['count' => $count]));
				}
				return $this->redirect(['view', 'id' => $id]);
			} catch (MissingProvisionUserException $exception) {
				Flash::add(Flash::TYPE_ERROR, $exception->getMessage());
			}
		}

		return $this->render('user', [
			'model' => $model,
			'navTypesItems' => $navTypesItems,
			'userNotSettledCosts' => $userNotSettledCosts,
			'settlementCostDataProvider' => $settlementCostDataProvider,
		]);
	}

	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$count = Yii::$app->provisions->removeForPays($model->getPays()->getIds());
		if ($count) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('provision', 'Remove {count} provisions.', ['count' => $count]));
		}

		return $this->redirect(['view', 'id' => $id]);
	}

	public function actionDeleteMulti(array $ids) {
		$count = 0;
		foreach ($ids as $id) {
			$model = $this->findModel($id);
			$count += Yii::$app->provisions->removeForPays($model->getPays()->getIds());
		}
		if ($count) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('provision', 'Remove {count} provisions.', ['count' => $count]));
		}
		return $this->redirect(['/settlement/calculation/index']);
	}

	private function findModel(int $id): IssueSettlement {
		$model = IssuePayCalculation::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}
