<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\entity\search\LawsuitSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class LawsuitController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors(): array {
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'proceedings' => ['POST'],
						'sessions' => ['POST'],
					],
				],
			]
		);
	}

	private LawsuitRepository $repository;

	public function init(): void {
		parent::init();
		$this->repository = $this->module->getRepositoryManager()->getLawsuits();
		$this->view->params['appeal'] = $this->module->getAppeal();
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

	public function actionView(string $id): string {
		$model = $this->findModel($id);

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionParties(int $id): string {
		$repository = $this->module
			->getRepositoryManager()
			->getParties();
		$dataProvider = $repository->getByLawsuit($id);
		$dataProvider->setPagination(false);
		$dataProvider->getSort()->attributes = [];
		$html = $this->renderPartial('parties', [
			'dataProvider' => $dataProvider,
		]);
		return Json::encode($html);
	}

	public function actionSessions(int $id): string {
		$repository = $this->module
			->getRepositoryManager()
			->getCourtSessions();
		$dataProvider = $repository->getByLawsuit($id);
		$dataProvider->setPagination(false);
		$dataProvider->getSort()->attributes = [];
		$html = $this->renderPartial('sessions', [
			'dataProvider' => $dataProvider,
		]);
		return Json::encode($html);
	}

	public function actionProceedings(int $id): string {
		$repository = $this->module
			->getRepositoryManager()
			->getProceedings();
		$dataProvider = $repository->getByLawsuit($id);
		$dataProvider->setPagination(false);
		$dataProvider->getSort()->defaultOrder = ['date' => SORT_DESC];
		$dataProvider->prepare();
		$dataProvider->getSort()->attributes = [];
		$html = $this->renderPartial('proceedings', [
			'dataProvider' => $dataProvider,
		]);
		return Json::encode($html);
	}

	private function findModel(string $id): LawsuitDetailsDto {
		$model = $this->repository
			->getLawsuit($id);
		if (empty($model)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
