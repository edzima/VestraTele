<?php

namespace frontend\controllers;

use common\models\issue\IssuePay;
use common\models\settlement\PayReceivedForm;
use common\models\user\Worker;
use frontend\models\search\PayReceivedSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PayReceivedController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_PAY_RECEIVED],
					],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new PayReceivedSearch();
		$searchModel->user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->post());
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionReceived(int $id) {
		$model = new PayReceivedForm(Yii::$app->user->getId(), $this->findModel($id));
		$model->date = date('Y-m-d');
		if ($model->load(Yii::$app->request->post()) && $model->received()) {
			return $this->redirect(['index']);
		}
		return $this->render('received', [
			'model' => $model,
		]);
	}

	private function findModel(int $id): IssuePay {
		$model = IssuePay::find()
			->andWhere(['id' => $id])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
