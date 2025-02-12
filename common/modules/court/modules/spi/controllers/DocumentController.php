<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\DocumentRepository;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class DocumentController extends Controller {

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
						'lawsuit' => ['POST'],
					],
				],
			]
		);
	}

	private DocumentRepository $repository;

	public function init(): void {
		parent::init();
		$appeal = $this->module->getAppeal();
		$this->view->params['appeal'] = $appeal;
		$this->repository = $this->module
			->getRepositoryManager()
			->getDocuments()
			->setAppeal($appeal);
	}

	public function actionLawsuit(int $id): string {
		$dataProvider = $this->repository->getByLawsuit($id);
		$dataProvider->getSort()->defaultOrder = ['createDate' => SORT_DESC];
		$dataProvider->prepare();
		$dataProvider->getSort()->attributes = [];
		$html = $this->renderPartial('lawsuit', [
			'dataProvider' => $dataProvider,
		]);

		return Json::encode($html);
	}

	public function actionDownload(int $id, string $fileName) {
		$file = $this->repository->download($id);
		if ($file) {
			return Yii::$app->response->sendContentAsFile($file, $fileName);
		}
		throw new NotFoundHttpException();
	}

	public function actionPdf(int $id, string $fileName) {
		$file = $this->repository->download($id, true);
		if ($file) {
			return Yii::$app->response->sendContentAsFile($file, $fileName, [
				'mimeType' => 'application/pdf',
				'inline' => true,
			]);
		}
		throw new NotFoundHttpException();
	}

}
