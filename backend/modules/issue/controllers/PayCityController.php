<?php

namespace backend\modules\issue\controllers;

use Yii;
use common\models\issue\IssuePayCity;
use common\models\issue\IssuePayCitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayCityController implements the CRUD actions for IssuePayCity model.
 */
class PayCityController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssuePayCity models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssuePayCitySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePayCity model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssuePayCity model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int|null $city_id
	 * @return mixed
	 */
	public function actionCreate(int $city_id = null) {

		if ($city_id !== null && IssuePayCity::findOne($city_id) !== null) {
			$this->redirect(['view', 'id' => $city_id]);
		}
		$model = new IssuePayCity(['city_id' => $city_id]);
		$data = Yii::$app->request->post();
		if ($model->getAddress()->load($data) && $model->getAddress()->validate()) {
			$model->city_id = $model->getAddress()->cityId;
			if ($model->load($data) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->city_id]);
			}
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssuePayCity model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);
		$data = Yii::$app->request->post();
		if ($model->getAddress()->load($data) && $model->getAddress()->validate()) {
			$model->city_id = $model->getAddress()->cityId;
			if ($model->load($data) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->city_id]);
			}
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssuePayCity model.
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
	 * Finds the IssuePayCity model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePayCity the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = IssuePayCity::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
