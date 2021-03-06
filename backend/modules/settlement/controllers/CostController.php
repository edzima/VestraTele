<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\IssueCostForm;
use backend\modules\settlement\models\search\IssueCostSearch;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CostController implements the CRUD actions for IssueCost model.
 */
class CostController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
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
	 * Lists all IssueCost models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$model = new IssueCostSearch();
		$dataProvider = $model->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssueCost models for Issue.
	 *
	 * @param int $id
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionIssue(int $id): string {
		$issue = $this->findIssue($id);
		$model = new IssueCostSearch();
		$model->issue_id = $id;
		$dataProvider = $model->search(Yii::$app->request->queryParams);

		return $this->render('issue', [
			'model' => $model,
			'issue' => $issue,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssue(int $id): Issue {
		$model = Issue::find()->andWhere(['id' => $id]);
		if (!Yii::$app->user->can(User::PERMISSION_ARCHIVE)) {
			$model->withoutArchives();
		}
		$model = $model->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

	/**
	 * Displays a single IssueCost model.
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
	 * Creates a new IssueCost model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $id) {
		$issue = $this->findIssue($id);
		$model = new IssueCostForm($issue);
		$model->date_at = date(DATE_ATOM);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueCost model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$cost = $this->findModel($id);
		$model = new IssueCostForm($cost->issue);
		$model->setModel($cost);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueCost model.
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

	/**
	 * Finds the IssueCost model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueCost the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueCost {
		if (($model = IssueCost::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
