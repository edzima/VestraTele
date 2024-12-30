<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\models\search\ApplicationSearch;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\web\Controller;

/**
 * @property Module $module
 */
class ApplicationController extends Controller {

	public function actionIndex(): string {
		$searchModel = new ApplicationSearch();
		$dataProvider = $searchModel->search(
			$this->module->getSpiApi(),
			Yii::$app->request->queryParams
		);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
