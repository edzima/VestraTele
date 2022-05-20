<?php

namespace common\modules\czater\controllers;

use common\modules\czater\entities\Call;
use yii\web\NotFoundHttpException;

class CallController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = $this->createDataProvider();
		$dataProvider->key = 'id';
		$dataProvider->models = $this->module->czater->getCalls($dataProvider->pagination->getOffset());
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	private function findModel(int $id): Call {
		$model = $this->module->czater->getCall($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
