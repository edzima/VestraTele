<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\entity\search\ApplicationSearch;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\web\Controller;

/**
 * @property Module $module
 */
class ApplicationController extends Controller {

	public function actionIndex(string $appeal = null): string {
		if ($appeal === null) {
			$appeal = $this->module->getAppeal();
		}
		$searchModel = new ApplicationSearch(
			$this->module->getRepositoryManager()->getApplications(),
			$appeal
		);
		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
