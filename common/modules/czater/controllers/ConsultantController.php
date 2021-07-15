<?php

namespace common\modules\czater\controllers;

use yii\data\ArrayDataProvider;

class ConsultantController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = new ArrayDataProvider([
			'allModels' => $this->module->czater->consultants(),
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
