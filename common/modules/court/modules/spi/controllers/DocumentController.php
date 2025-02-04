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
		$this->view->params['appeal'] = $this->module->getAppeal();
		$this->repository = $this->module
			->getRepositoryManager()
			->getDocuments();
	}

	public function actionLawsuit(int $id): string {
		$dataProvider = $this->repository->getByLawsuit($id);
		$dataProvider->getSort()->defaultOrder = ['createDate' => SORT_DESC];

		$html = $this->renderPartial('lawsuit', [
			'dataProvider' => $dataProvider,
		]);
		return Json::encode($html);
	}

	public function actionView(int $id, string $fileName) {
		$file = $this->repository->download($id);
		if ($file) {
			return Yii::$app->response->sendContentAsFile($file, $fileName);
		}
		throw new NotFoundHttpException();
	}

}
