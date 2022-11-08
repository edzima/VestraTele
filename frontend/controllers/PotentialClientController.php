<?php

namespace frontend\controllers;

use common\models\PotentialClient;
use common\models\user\Worker;
use frontend\models\search\PotentialClientSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PotentialClientController implements the CRUD actions for PotentialClient model.
 */
class PotentialClientController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'auth' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_POTENTIAL_CLIENT],
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
	 * Search PotentialClient models.
	 *
	 * @return mixed
	 */
	public function actionSearch(): string {
		$searchModel = new PotentialClientSearch();
		$searchModel->scenario = PotentialClientSearch::SCENARIO_REQUIRED_FIELDS;
		if (empty(Yii::$app->request->queryParams)) {
			$dataProvider = null;
		} else {
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		}
		return $this->render('search', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Search PotentialClient models.
	 *
	 * @return mixed
	 */
	public function actionSelf(): string {
		$searchModel = new PotentialClientSearch();
		$searchModel->owner_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('self', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single PotentialClient model.
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

	/**
	 * Finds the PotentialClient model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return PotentialClient the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): PotentialClient {
		if (($model = PotentialClient::findOne($id)) !== null) {
			if (!$model->isOwner(Yii::$app->user->getId())) {
				throw new NotFoundHttpException('This model is not Your.');
			}
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * Creates a new PotentialClient model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new PotentialClient();
		$model->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing PotentialClient model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing PotentialClient model.
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
}
