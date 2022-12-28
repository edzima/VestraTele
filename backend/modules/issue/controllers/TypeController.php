<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueTypeForm;
use backend\modules\issue\models\search\IssueTypeSearch;
use common\models\issue\IssueType;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * TypeController implements the CRUD actions for IssueType model.
 */
class TypeController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'stages-list' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssueType models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new IssueTypeSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueType model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new IssueTypeForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$model = new IssueTypeForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueType model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	public function actionStagesList(): array {
		$params = Yii::$app->request->post('depdrop_parents');
		if (empty($params)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		Yii::$app->response->format = Response::FORMAT_JSON;
		$id = (int) reset($params);
		$stages = $this->findModel($id)->stages;

		return [
			'output' => $stages,
			'selected' => (string) reset($stages)->id,
		];
	}

	/**
	 * Finds the IssueType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueType {
		if (($model = IssueType::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
