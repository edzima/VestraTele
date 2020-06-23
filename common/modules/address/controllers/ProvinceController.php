<?php

namespace common\modules\address\controllers;

use Yii;
use common\models\address\Province;
use common\models\address\search\ProvinceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ProvinceController implements the CRUD actions for Province model.
 */
class ProvinceController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
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
	 * Lists all Powiat models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ProvinceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Powiat model.
	 *
	 * @param integer $id
	 * @param integer $wojewodztwo_id
	 * @return mixed
	 */
	public function actionView($id, $wojewodztwo_id) {
		return $this->render('view', [
			'model' => $this->findModel($id, $wojewodztwo_id),
		]);
	}

	/**
	 * Creates a new Powiat model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Province();
		//@todo check created provinces
		//new Powiat ->38
		$model->id = 38;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Powiat model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @param integer $wojewodztwo_id
	 * @return mixed
	 */
	public function actionUpdate($id, $wojewodztwo_id) {
		$model = $this->findModel($id, $wojewodztwo_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Powiat model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @param integer $wojewodztwo_id
	 * @return mixed
	 */
	public function actionDelete($id, $wojewodztwo_id) {
		$this->findModel($id, $wojewodztwo_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Powiat model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param integer $wojewodztwo_id
	 * @return Province the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $wojewodztwo_id): Province {
		if (($model = Province::findOne(['id' => $id, 'wojewodztwo_id' => $wojewodztwo_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
