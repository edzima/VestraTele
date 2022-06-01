<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueClaimsForm;
use backend\modules\issue\models\search\ClaimSearch;
use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ClaimController implements the CRUD actions for IssueClaim model.
 */
class ClaimController extends Controller {

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
	 * Lists all IssueProvision models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new ClaimSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreateMultiple(int $issueId) {
		$issue = Issue::findOne($issueId);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}

		$model = new IssueClaimsForm($issue);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue/view', 'id' => $issueId]);
		}

		return $this->render('create-multiple', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new IssueProvision model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $issueId, string $type = IssueClaim::TYPE_COMPANY) {
		if ($type !== null && !isset(IssueClaim::getTypesNames()[$type])) {
			throw new NotFoundHttpException();
		}
		$model = new IssueClaim();
		$model->issue_id = $issueId;
		$model->type = $type;
		if ($model->issue === null) {
			throw new NotFoundHttpException();
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue/view', 'id' => $model->issue_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueProvision model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue/view', 'id' => $model->issue_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueProvision model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$model->delete();

		return $this->redirect(['issue/view', 'id' => $model->issue_id]);
	}

	/**
	 * Finds the IssueProvision model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueClaim the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueClaim {
		if (($model = IssueClaim::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
	}
}
