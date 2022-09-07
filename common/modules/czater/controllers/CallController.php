<?php

namespace common\modules\czater\controllers;

use yii\data\ArrayDataProvider;

class CallController extends BaseController {

	public function actionIndex(int $page = 0): string {
		$dataProvider = new ArrayDataProvider([
			'allModels' => $this->module->czater->calls($page),
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
