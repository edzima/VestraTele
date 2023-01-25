<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\StageTypeForm;
use common\models\issue\IssueStageType;
use common\models\issue\IssueType;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StageTypeController extends Controller {

	public function actionCreate(int $stage_id = null, int $type_id = null) {
		$model = new StageTypeForm();
		$model->scenario = StageTypeForm::SCENARIO_CREATE;
		$model->stage_id = $stage_id;
		$model->type_id = $type_id;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($type_id !== null) {
				return $this->redirect(['type/view', 'id' => $model->type_id]);
			}
			return $this->redirect(['stage/view', 'id' => $model->stage_id]);
		}

		return $this->render('create', [
			'model' => $model,
			'type' => $type_id !== null ? IssueType::get($type_id) : null,
			'stage' => $stage_id !== null ? IssueStage::get($stage_id) : null,
		]);
	}

	public function actionUpdate(int $stage_id, int $type_id) {
		$model = new StageTypeForm();
		$model->setModel($this->findModel($stage_id, $type_id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['stage/view', 'id' => $stage_id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	private function findModel(int $stage_id, int $type_id) {
		$model = IssueStageType::find()
			->andWhere(['stage_id' => $stage_id])
			->andWhere(['type_id' => $type_id])
			->one();

		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

	public function actionDelete(int $stage_id, int $type_id) {
		$model = $this->findModel($stage_id, $type_id);
		$model->delete();
		return $this->redirect(['stage/view', 'id' => $stage_id]);
	}

}
