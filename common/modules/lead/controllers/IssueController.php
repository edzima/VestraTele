<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\LeadIssue;
use common\modules\lead\models\searches\LeadIssueSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * IssueController implements the CRUD actions for LeadIssue model.
 */
class IssueController extends BaseController {

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
	 * Lists all LeadIssue models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new LeadIssueSearch();
		$searchModel->currentCrmAppId = Yii::$app->issuesLeads->getCrmId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionConfirm(int $lead_id, int $issue_id, string $returnUrl = null) {
		$model = $this->findModel($lead_id, $issue_id, Yii::$app->issuesLeads->getCrmId());
		$model->confirmed_at = date(DATE_ATOM);
		$model->save();
		//@todo add Confirm Model with Email
		if (empty($returnUrl)) {
			return $this->redirect(['index']);
		}
		return $this->redirect($returnUrl);
	}

	public function actionUnconfirm(int $lead_id, int $issue_id, string $returnUrl = null) {
		$model = $this->findModel($lead_id, $issue_id, Yii::$app->issuesLeads->getCrmId());
		$model->confirmed_at = null;
		$model->save();
		if (empty($returnUrl)) {
			return $this->redirect(['index']);
		}
		return $this->redirect($returnUrl);
	}

	/**
	 * Displays a single LeadIssue model.
	 *
	 * @param integer $lead_id
	 * @param integer $issue_id
	 * @param integer $crm_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $lead_id, int $issue_id, int $crm_id) {
		return $this->render('view', [
			'model' => $this->findModel($lead_id, $issue_id, $crm_id),
		]);
	}

	/**
	 * Creates a new LeadIssue model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadIssue();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'lead_id' => $model->lead_id, 'issue_id' => $model->issue_id, 'crm_id' => $model->crm_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadIssue model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $lead_id
	 * @param integer $issue_id
	 * @param integer $crm_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $lead_id, int $issue_id, int $crm_id) {
		$model = $this->findModel($lead_id, $issue_id, $crm_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'lead_id' => $model->lead_id, 'issue_id' => $model->issue_id, 'crm_id' => $model->crm_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadIssue model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $lead_id
	 * @param integer $issue_id
	 * @param integer $crm_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $lead_id, int $issue_id, int $crm_id) {
		$this->findModel($lead_id, $issue_id, $crm_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadIssue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $lead_id
	 * @param integer $issue_id
	 * @param integer $crm_id
	 * @return LeadIssue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $lead_id, int $issue_id, int $crm_id): LeadIssue {
		if (($model = LeadIssue::findOne(['lead_id' => $lead_id, 'issue_id' => $issue_id, 'crm_id' => $crm_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
