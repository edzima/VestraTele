<?php

namespace common\modules\address\controllers;

use Yii;
use common\models\address\City;
use common\models\address\Province;
use common\models\address\SubProvince;
use common\models\address\search\CitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

use yii\filters\AccessControl;

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
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all City models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CitySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
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
		$model = new City();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
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
		}
		return $this->render('update', [
			'model' => $model,
		]);
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

		return $this->redirect(['index']);
	}

	public function actionPowiat() {
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$cat_id = $parents[0];
				$out = Province::getPowiatListId($cat_id);
				return Json::encode(['output' => $out, 'selected' => '']);
			}
		}
		return Json::encode(['output' => '', 'selected' => '']);
	}

	public function actionGmina() {
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id != null && is_numeric($subcat_id)) {
				$data = SubProvince::getGminaList($cat_id, $subcat_id);
				return Json::encode(['output' => $data['out'], 'selected' => $data['selected']]);
			}
		}
		return Json::encode(['output' => '', 'selected' => '']);
	}

	public function actionCity() {
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id != null && is_numeric($subcat_id)) {
				$data = City::getCitiesList($cat_id, $subcat_id);
				return Json::encode(['output' => $data['out'], 'selected' => $data['selected']]);
			}
		}
		return Json::encode(['output' => '', 'selected' => '']);
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
