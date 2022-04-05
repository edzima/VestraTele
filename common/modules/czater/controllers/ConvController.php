<?php

namespace common\modules\czater\controllers;

class ConvController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = $this->createDataProvider();
		$dataProvider->models = $this->module->czater->getConvs($dataProvider->pagination->getOffset());
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
