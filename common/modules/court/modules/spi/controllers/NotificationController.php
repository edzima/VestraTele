<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\models\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\NotificationsRepository;
use Yii;
use yii\web\Controller;

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
}
