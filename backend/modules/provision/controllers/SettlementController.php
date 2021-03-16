<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use common\helpers\Flash;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SettlementController extends Controller {

	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$dataProvider = new ActiveDataProvider([
			'query' => Provision::find()
				->andWhere(['pay_id' => $model->getPays()->getIds()])
				->orderBy(['value' => SORT_DESC]),
			'pagination' => false,
			'sort' => false,
		]);
		$userModels = [];
		foreach ($model->issue->users as $issueUser) {
			$userModels[] = new SettlementUserProvisionsForm($model, $issueUser->type);
		}

		if (empty(IssueProvisionType::findCalculationTypes($model))) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'Not active types for settlement.')
			);
		}

		Url::remember();

		return $this->render('view', [
			'model' => $model,
			'dataProvider' => $dataProvider,
			'userModels' => $userModels,
		]);
	}

	public function actionUser(int $id, string $issueUserType, int $typeId = null) {
		$settlement = $this->findModel($id);

		try {
			$model = new SettlementUserProvisionsForm($settlement, $issueUserType);
		} catch (InvalidConfigException $e) {
			throw new NotFoundHttpException($e->getMessage());
		}

		if ($typeId) {
			try {
				$model->setType($this->findType($typeId));
			} catch (InvalidConfigException $e) {
				throw new NotFoundHttpException();
			}
		} else {
			$types = $model->getTypes();
			$type = reset($types);
			if ($type instanceof IssueProvisionType) {
				return $this->redirect(['user', 'id' => $id, 'issueUserType' => $issueUserType, 'typeId' => $type->id]);
			}
		}

		if (empty($model->getTypes())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'Not active {userType} types for settlement.', ['userType' => $model->getIssueUser()->getTypeName()])
			);
		}

		$navTypesItems = [];
		foreach ($model->getTypes() as $type) {
			$navTypesItems[] = [
				'label' => $type->getNameWithTypeName(),
				'url' => ['user', 'id' => $id, 'issueUserType' => $issueUserType, 'typeId' => $type->id],
				'active' => $type->id === $typeId,
			];
		}

		Url::remember();

		$userNotSettledCosts = new ActiveDataProvider([
			'query' => IssueCost::find()
				->notSettled()
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
					$settlement->getCostsWithoutUser()),
				'modelClass' => IssueCost::class,
			]
		);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
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

	private function findModel(int $id): IssuePayCalculation {
		$model = IssuePayCalculation::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

	private function findType(int $id): IssueProvisionType {
		$model = IssueProvisionType::getType($id, true);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}
