<?php

namespace frontend\controllers;

use common\modules\lead\components\LeadDialerManager;
use common\modules\lead\Module;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property-read Module $module
 */
class LeadDialerController extends Controller {

	private LeadDialerManager $dialer;

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
				'not-answered' => ['POST'],
			],
		];
		return $behaviors;
	}

	public function beforeAction($action) {
		$before = parent::beforeAction($action);
		$this->dialer = $this->module->getDialer();
		return $before;
	}

	public function actionCall() {
		$data = $this->dialer->calling();
		if ($data === null) {
			throw new NotFoundHttpException();
		}

		return $this->asJson($data);
	}

	public function actionAnswered(int $id) {
		$success = $this->dialer->answer($id);
		return $this->asJson([
			'success' => $success,
		]);
	}

	public function actionNotAnswered(int $id) {
		$success = $this->dialer->notAnswer($id);
		return [
			'success' => $success,
		];
	}

}
