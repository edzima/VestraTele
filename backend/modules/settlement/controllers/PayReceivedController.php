<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\ReceivePaysForm;
use backend\modules\settlement\models\search\PayReceivedSearch;
use common\models\issue\IssuePay;
use common\models\settlement\PayReceived;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PayReceivedController implements the CRUD actions for PayReceived model.
 */
class PayReceivedController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'user-not-transfer-pays' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all PayReceived models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new PayReceivedSearch();
		$searchModel->transferStatus = PayReceivedSearch::TRANFER_STATUS_NO;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionReceive() {
		$model = new ReceivePaysForm();
		$model->date = date('Y-m-d');
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->addFlash('success',
				Yii::t('backend', 'Received {count} pays. Sum value: {sumValue}', [
					'count' => count($model->pays_ids),
					'sumValue' => Yii::$app->formatter->asCurrency(
						IssuePay::find()->andWhere(['id' => $model->pays_ids])->sum('value')
					),
				]));

			return $this->redirect(['index']);
		}
		return $this->render('receive', ['model' => $model]);
	}

	public function actionUserNotTransferPays() {
		$params = Yii::$app->request->post('depdrop_parents');
		if (empty($params)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		Yii::$app->response->format = Response::FORMAT_JSON;
		$user_id = (int) reset($params);
		$pays = (new ReceivePaysForm(['user_id' => $user_id]))->getNotTransferPays();
		$data = [];
		foreach ($pays as $pay) {
			$data[$pay->pay_id] =
				[
					'id' => $pay->pay_id,
					'name' => ReceivePaysForm::getName($pay),
				];
		}

		return [
			'output' => $data,
			'selected' => '',
		];
	}

	/**
	 * Updates an existing PayReceived model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->pay_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing PayReceived model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the PayReceived model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return PayReceived the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id) {
		if (($model = PayReceived::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('settlement', 'The requested page does not exist.'));
	}
}
