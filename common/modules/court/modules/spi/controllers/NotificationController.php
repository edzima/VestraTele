<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\entity\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\NotificationsRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class NotificationController extends Controller {

	public bool $readOnView = true;
	private NotificationsRepository $repository;

	public function init(): void {
		parent::init();
		$this->repository = $this->module->getRepositoryManager()->getNotifications();
		$this->repository->setAppeal($this->module->getAppeal());
	}

	public function actionIndex(string $appeal = null) {
		if ($appeal === null) {
			$appeal = $this->module->getAppeal();
		}
		$searchModel = new NotificationSearch(
			$this->repository,
			$appeal
		);
		$searchModel->lawsuitRepository = $this->module->getRepositoryManager()->getLawsuits();
		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);
		$dataProvider->getSort()->defaultOrder = ['date' => SORT_DESC];
		$this->repository->getUnread(false);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionRead(string $appeal, int $id, string $signature = null, string $court = null) {
		//@todo Because in Doc API, read action should return NotificationDTO, but return Boolean.
		$this->repository->read($id);
		return $this->redirect(['index', 'appeal' => $appeal, 'id' => $id]);
	}

	public function actionView(int $id, string $appeal, string $signature = null, string $court = null) {
		$model = $this->repository->findModel($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $this->render('view', [
			'model' => $model,
			'appeal' => $appeal,
		]);
	}

}
