<?php

namespace backend\controllers;

use Yii;
use common\models\Task;
use backend\models\search\TaskSearch;
use common\models\Wojewodztwa;
use common\models\AccidentTyp;
use common\models\TaskStatusSearch;

use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller {

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
	 * Lists all Task models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TaskSearch();

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$searchStatus = new TaskStatusSearch();
		$statusProvider = $searchStatus->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'searchStatus' => $searchStatus,
			'statusProvider' => $statusProvider,
		]);
	}

	/**
	 * Displays a single Task model.
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
	 * Creates a new Task model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Task();

		$woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
		$accident = ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name');

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				return $this->redirect(['view', 'id' => $model->id]);
			}
		} else {
			return $this->render('create', [
				'model' => $model,
				'woj' => $woj,
				'accident' => $accident,
			]);
		}
	}

	/**
	 * Updates an existing Task model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		$woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
		$accident = ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name');

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
			'woj' => $woj,
			'accident' => $accident,
		]);
	}

	/**
	 * Deletes an existing Task model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Task model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Task the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */

	protected function findModel($id): Task {
		if (($model = Task::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
