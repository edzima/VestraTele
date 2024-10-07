<?php

namespace backend\modules\entityResponsible\controllers;

use backend\modules\entityResponsible\models\EntityResponsibleForm;
use backend\modules\issue\models\search\IssueSearch;
use common\models\entityResponsible\EntityResponsible;
use common\models\entityResponsible\EntityResponsibleSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EntityResponsibleController implements the CRUD actions for IssueEntityResponsible model.
 */
class DefaultController extends Controller {

	/**
	 * @inheritdoc
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
	 * Lists all IssueEntityResponsible models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new EntityResponsibleSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueEntityResponsible model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$searchModel = new IssueSearch();
		$searchModel->entity_responsible_id = $id;
		$searchModel->userId = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('view', [
			'model' => $model,
			'issueFilterModel' => $searchModel,
			'issueDataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new IssueEntityResponsible model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new EntityResponsibleForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueEntityResponsible model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$model = new EntityResponsibleForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueEntityResponsible model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueEntityResponsible model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return EntityResponsible the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): EntityResponsible {
		if (($model = EntityResponsible::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
