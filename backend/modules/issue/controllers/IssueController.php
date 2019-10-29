<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueForm;
use common\models\User;
use Yii;
use common\models\issue\Issue;
use common\models\issue\IssueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IssueController implements the CRUD actions for Issue model.
 */
class IssueController extends Controller {

	/**
	 * @inheritdoc
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
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Issue model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		if ($model->isPositiveDecision() && $model->pay_city_id === null) {
			Yii::$app->session->addFlash('warning', 'Nie ustalono miejscowści wypłat');
		}
		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new Issue model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$form = new IssueForm();
		if ($form->load(Yii::$app->request->post()) && $form->save()) {
			return $this->redirect(['view', 'id' => $form->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $form,
		]);
	}

	/**
	 * Updates an existing Issue model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$form = new IssueForm(['model' => $this->findModel($id)]);
		if ($form->load(Yii::$app->request->post()) && $form->save()) {
			return $this->redirect(['index']);
		}
		return $this->render('update', [
			'model' => $form,
		]);
	}

	/**
	 * Deletes an existing Issue model.
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
	 * Finds the Issue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Issue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
