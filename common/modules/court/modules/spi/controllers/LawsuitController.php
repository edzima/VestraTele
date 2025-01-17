<?php

namespace common\modules\court\modules\spi\controllers;

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

	private LawsuitRepository $repository;

	public function init(): void {
		parent::init();
		$this->repository = $this->module->getRepositoryManager()->getLawsuit();
	}

	public function actionIndex(string $appeal = null) {
		if ($appeal === null) {
			$appeal = $this->module->getAppeal();
		}
		$searchModel = new LawsuitSearch(
			$this->repository,
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

	public function actionView(string $id, string $appeal): string {
		$model = $this->findModel($id, $appeal);

		return $this->render('view', ['model' => $model]);
	}

	private function findModel(string $id, string $appeal): LawsuitDetailsDto {
		$model = $this->repository->getLawsuit($id, $appeal);
		if (empty($model)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
