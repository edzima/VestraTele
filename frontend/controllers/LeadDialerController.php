<?php

namespace frontend\controllers;

use common\modules\lead\components\DialerManager;
use common\modules\lead\entities\DialerInterface;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LeadDialerController extends Controller {

	private DialerManager $dialer;

	public function behaviors(): array {
		$behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => HttpHeaderAuth::class,
			'header' => 'Dialer-Api-Key',
		];
		$behaviors['verb'] = [
			'class' => VerbFilter::class,
			'actions' => [
				'answered' => ['POST'],
				'call' => ['POST'],
				'calling' => ['POST'],
				'extension' => ['POST'],
				'queue' => ['POST'],
				'not-answered' => ['POST'],
			],
		];
		return $behaviors;
	}

	public function beforeAction($action) {
		$before = parent::beforeAction($action);
		$this->dialer = new DialerManager();
		return $before;
	}

	public function actionCall() {
		$model = $this->dialer->findToCall();
		if ($model && $this->dialer->calling($model)) {
			return $this->asJson([
				'id' => $model->getID(),
				'origin' => $model->getOrigin(),
				'destination' => $model->getDestination(),
			]);
		}
		throw new NotFoundHttpException();
	}

	public function actionExtension() {
		$this->dialer->type = DialerManager::TYPE_EXTENSION;
		$model = $this->dialer->findToCall();
		if ($model) {
			return $this->asJson([
				'id' => $model->getID(),
				'origin' => $model->getOrigin(),
				'destination' => $model->getDestination(),
				'did' => $model->getDID(),
			]);
		}
		throw new NotFoundHttpException();
	}

	public function actionQueue() {
		$this->dialer->type = DialerManager::TYPE_QUEUE;

		$model = $this->dialer->findToCall();
		if ($model) {
			return $this->asJson([
				'id' => $model->getID(),
				'origin' => $model->getOrigin(),
				'destination' => $model->getDestination(),
				'did' => $model->getDID(),
			]);
		}
		throw new NotFoundHttpException();
	}

	public function actionCalling(int $id) {
		$success = $this->dialer->calling($this->findModel($id));
		return $this->asJson([
			'success' => $success,
		]);
	}

	public function actionAnswered(int $id): Response {
		$success = $this->dialer->establish($this->findModel($id));
		return $this->asJson([
			'success' => $success,
		]);
	}

	public function actionNotAnswered(int $id): Response {
		$success = $this->dialer->notEstablish($this->findModel($id));
		return $this->asJson([
			'success' => $success,
		]);
	}

	protected function findModel(int $id): DialerInterface {
		$model = $this->dialer->find($id);
		if ($model) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

}
