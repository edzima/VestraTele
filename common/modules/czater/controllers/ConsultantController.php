<?php

namespace common\modules\czater\controllers;

class ConsultantController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = $this->createDataProvider();
		$dataProvider->models = $this->module->czater->getConsultants();
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
