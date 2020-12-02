<?php

namespace backend\modules\provision\controllers;

use backend\modules\provision\models\SettlementProvisionsForm;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SettlementController extends Controller {

	public function actionSet(int $id) {
		$settlement = $this->findModel($id);

		$model = new SettlementProvisionsForm($settlement);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/settlement/calculation/view', 'id' => $id]);
		}
		return $this->render('set', [
			'model' => $model,
		]);
	}

	protected function findModel(int $id): IssuePayCalculation {
		$model = IssuePayCalculation::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
