<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\PayForm;
use common\models\issue\Issue;
use Yii;
use common\models\issue\IssuePay;
use common\models\issue\IssuePaySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayController implements the CRUD actions for IssuePay model.
 */
class PayController extends Controller {

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
	 * Lists all IssuePay models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssuePaySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePay model.
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
	 * Creates a new IssuePay model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $issueId) {
		$pay = new IssuePay();
		$pay->issue_id = $this->findIssueModel($issueId)->id;

		$model = new PayForm($pay);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirectIssue($issueId);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssuePay model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = new PayForm($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirectIssue($model->pay->issue_id);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssuePay model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$model = $this->findModel($id);
		$issueId = $model->issue_id;
		$model->delete();
		$this->redirectIssue($issueId);
	}

	private function redirectIssue(int $issueId) {
		return $this->redirect(['issue/view', 'id' => $issueId]);
	}

	/**
	 * Finds the IssuePay model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePay the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssuePay {
		if (($model = IssuePay::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	protected function findIssueModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
