<?php

namespace common\modules\czater\controllers;

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
		$model = $this->module->czater->getCall($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}
}
