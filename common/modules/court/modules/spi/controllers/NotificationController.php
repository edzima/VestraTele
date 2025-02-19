<?php

namespace common\modules\court\modules\spi\controllers;

use common\modules\court\modules\spi\entity\AppealInterface;
use common\modules\court\modules\spi\entity\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\NotificationsRepository;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property Module $module
 */
class NotificationController extends Controller {

	public bool $readOnView = true;
	protected NotificationsRepository $repository;
	public string $defaultAppeal = AppealInterface::APPEAL_WROCLAW;

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'read' => ['POST'],
					'read-all' => ['POST'],
				],
			],
		];
	}

	public function actionIndex(string $appeal = null) {
		if ($appeal === null) {
			$appeal = $this->defaultAppeal;
			if (empty($this->module->appeal)) {
				$this->module->appeal = $appeal;
			}
		}
		$searchModel = new NotificationSearch(
			$this->getRepository(),
			$appeal
		);
		$searchModel->read = false;
		$searchModel->lawsuitRepository = $this->module
			->getRepositoryManager()
			->getLawsuits();
		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);
		$dataProvider->getSort()->defaultOrder = ['date' => SORT_DESC];

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'unreadCount' => $this->getRepository()->getUnread(),
		]);
	}

	public function actionView(int $id, string $appeal, string $signature = null, string $court = null) {
		$model = $this->getRepository()
			->setAppeal($appeal)
			->findModel($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $this->render('view', [
			'model' => $model,
			'appeal' => $appeal,
		]);
	}

	public function actionRead(string $appeal, int $id, string $signature = null, string $court = null, string $returnUrl = null) {
		//@todo Because in Doc API, read action should return NotificationDTO, but return Boolean.
		$this->getRepository()
			->setAppeal($appeal)
			->read($id);
		$url = $returnUrl ?: ['index', 'appeal' => $appeal];
		return $this->redirect($url);
	}

	public function actionReadAll(string $appeal, string $returnUrl = null) {
		$this->getRepository()
			->setAppeal($appeal)
			->readAll();
		$this->getRepository()->getUnread(false);
		$url = $returnUrl ?: ['index', 'appeal' => $appeal];
		return $this->redirect($url);
	}

	protected function getRepository(): NotificationsRepository {
		return $this->module->getRepositoryManager()->getNotifications();
	}

}
