<?php

namespace common\modules\czater\controllers;

class ClientController extends BaseController {

	public function actionIndex(): string {
		$dataProvider = $this->createDataProvider();
		$dataProvider->models = $this->module->czater->getClients($dataProvider->pagination->getOffset());
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

}
