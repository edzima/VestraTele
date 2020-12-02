<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\CalculationMinCountForm;
use common\models\issue\StageType;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CalculationMinCountController extends Controller {

	public function actionIndex(): string {
		$dataProvider = new ActiveDataProvider([
			'query' => StageType::find()
				->andWhere('min_calculation_count IS NOT NULL'),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionSet() {
		$model = new CalculationMinCountForm();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('index');
		}
		return $this->render('set', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $stage_id, int $type_id) {
		$stageType = $this->findModel($stage_id, $type_id);
		$model = new CalculationMinCountForm();
		$model->stageId = $stage_id;
		$model->typeId = $type_id;
		$model->minCount = (int) $stageType->min_calculation_count;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('index');
		}
		return $this->render('set', [
			'model' => $model,
		]);
	}

	private function findModel(int $stage_id, int $type_id): StageType {
		$model = StageType::find()->andWhere(['stage_id' => $stage_id, 'type_id' => $type_id])->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}
