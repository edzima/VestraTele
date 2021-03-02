<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\SettlementProvisionsForm;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use common\helpers\Flash;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
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

		$types = IssueProvisionType::findCalculationTypes($model);
		if (empty($types)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'Not active types for settlement.')
			);
		}

		Url::remember();

		return $this->render('view', [
			'model' => $model,
			'dataProvider' => $dataProvider,
			'types' => $types,
		]);
	}

	public function actionSet(int $id) {
		$settlement = $this->findModel($id);

		$model = new SettlementProvisionsForm($settlement);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::previous());
		}
		return $this->render('set', [
			'model' => $model,
		]);
	}

	public function actionUser(int $id, string $issueUserType, int $typeId = null) {
		$settlement = $this->findModel($id);
		$model = new SettlementUserProvisionsForm($settlement, $issueUserType);

		if (empty($model->getTypes())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'Not active types for settlement.')
			);
		}

		if ($typeId) {
			try {
				$model->setType($this->findType($typeId));
			} catch (InvalidConfigException $e) {
				throw new NotFoundHttpException($e->getMessage());
			}
		} else {
			$types = $model->getTypes();
			$type = reset($types);
			if ($type instanceof IssueProvisionType) {
				return $this->redirect(['user', 'id' => $id, 'issueUserType' => $issueUserType, 'typeId' => $type->id]);
			}
		}

		Url::remember();

		$userCostWithoutSettlementsDataProvider = new ActiveDataProvider([
			'query' => IssueCost::find()
				->andWhere(['user_id' => $model->getIssueUser()->user_id])
				->withoutSettlements(),
		]);

		$settlementCostDataProvider = new ActiveDataProvider([
				'query' => $settlement->getCosts()
					->andWhere([
						'or', [
							'user_id' => null,
							'user_id' => $model->getIssueUser()->user_id,
						],
					]),
			]
		);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('user', [
			'model' => $model,
			'userCostWithoutSettlementsDataProvider' => $userCostWithoutSettlementsDataProvider,
			'settlementCostDataProvider' => $settlementCostDataProvider,
		]);
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
