<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\models\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\NotificationsRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class NotificationController extends Controller {

	private NotificationsRepository $repository;

	public function init(): void {
		parent::init();
		$this->repository = $this->module->getRepositoryManager()->getNotifications();
	}

	public function actionIndex(string $appeal = null) {
		if ($appeal === null) {
			$appeal = $this->module->getAppeal();
		}
		$searchModel = new NotificationSearch(
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

	public function actionRead(string $appeal, int $id, string $signature = null, string $court = null) {
		//@todo Because in Doc API, read action should return NotificationDTO, but return Boolean.
		$this->repository->read($id, $appeal);
		return $this->redirect(['index', 'appeal' => $appeal, 'id' => $id]);
	}

	public function actionView(string $appeal, int $id) {
		$model = $this->repository->findModel($id, $appeal);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $this->render('view', [
			'model' => $model,
		]);
	}

}
