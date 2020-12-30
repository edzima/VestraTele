<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\search\PayReceivedSearch;
use common\models\settlement\PayReceived;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single PayReceived model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionReceived(int $id) {
		$model = PayReceived::find()
			->andWhere(['pay_id' => $id])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->goBack();
		}

		return $this->render('received', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing PayReceived model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
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
	public function actionDelete($id) {
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
	protected function findModel($id) {
		if (($model = PayReceived::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('settlement', 'The requested page does not exist.'));
	}
}
