<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\search\LawsuitSearch;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class LawsuitController extends Controller {

	private SPIApi $api;

	public function init(): void {
		parent::init();
		$this->api = $this->module->getSpiApi();
	}

	public function actionIndex(): string {
		$searchModel = new LawsuitSearch();
		$dataProvider = $searchModel->search(
			$this->api,
			Yii::$app->request->queryParams
		);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionView(string $id): string {
		$model = $this->findModel($id);

		return $this->render('view', ['model' => $model]);
	}

	private function findModel(string $id): LawsuitDetailsDto {
		$api = $this->api;
		$model = $api->getLawsuit($id);
		if (empty($model)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
