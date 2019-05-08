<?php

namespace frontend\controllers;

use Yii;
use common\models\City;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

use yii\filters\AccessControl;
use common\models\Powiat;
use common\models\Gmina;

/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Displays a single City model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new City model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$city = new City();

		if ($city->load(Yii::$app->request->post()) && $city->save()) {
			return $this->redirect(['view', 'id' => $city->id]);
		} else {
			return $this->render('create', [
				'city' => $city,
			]);
		}
	}

	/**
	 * Updates an existing City model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'city' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing City model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['create']);
	}

	public function actionPowiat() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$cat_id = $parents[0];
				$out = Powiat::getPowiatListId($cat_id);
				echo Json::encode(['output' => $out, 'selected' => '']);
				return;
			}
		}
		echo Json::encode(['output' => '', 'selected' => '']);
	}

	public function actionGmina() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id != null && is_numeric($subcat_id)) {
				$data = Gmina::getGminaList($cat_id, $subcat_id);
				echo Json::encode(['output' => $data['out'], 'selected' => $data['selected']]);
				return;
			}
		}
		echo Json::encode(['output' => '', 'selected' => '']);
	}

	public function actionCity() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id !== null && is_numeric($subcat_id)) {
				$data = City::getCitiesList($cat_id, $subcat_id);
				echo Json::encode(['output' => $data['out'], 'selected' => $data['selected']]);
				return;
			}
		}
		echo Json::encode(['output' => '', 'selected' => '']);
	}

	/**
	 * Finds the City model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return City the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): City {
		if (($model = City::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
