<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\search\LawsuitSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class LawsuitController extends Controller {

	private SPIApi $api;
	private LawsuitRepository $repository;

	public function init(): void {
		parent::init();
		$this->repository = new LawsuitRepository($this->module->spiApi);
		///	$this->api = $this->module->getSpiApi();
	}

	public function actionIndex(): string {
		$searchModel = new LawsuitSearch($this->repository);
		$dataProvider = $searchModel->search(
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
		$model = $this->repository->getLawsuit($id);
		if (empty($model)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
