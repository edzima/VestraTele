<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\IssueTagsLinkForm;
use backend\modules\issue\models\search\TagSearch;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueTag;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TagController implements the CRUD actions for IssueTag model.
 */
class TagController extends Controller {

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

	public function actionIssue(int $issueId, string $returnUrl = null) {
		$issue = Issue::findOne($issueId);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}
		$model = new IssueTagsLinkForm();
		$model->setIssueTags($issueId);
		if ($model->load(Yii::$app->request->post()) && $model->linkIssue($issueId)) {

			return $this->redirect($returnUrl !== null
				? $returnUrl
				: ['issue/view', 'id' => $issueId]
			);
		}
		return $this->render('issue', [
			'model' => $model,
			'issue' => $issue,
		]);
	}

	public function actionLinkMultiple(array $ids = []) {
		if (empty($ids)) {
			$ids = IssueController::getSelectionSearchIds();
		}

		$model = new IssueTagsLinkForm();
		$model->setScenario(IssueTagsLinkForm::SCENARIO_MULTIPLE_ISSUES);
		$model->issuesIds = $ids;

		if ($model->load(Yii::$app->request->post()) && $model->linkMultiple()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('backend', 'Linked {tagsCount} Tags to {issuesCount} Issues.', [
					'tagsCount' => count($model->getTagsIds()),
					'issuesCount' => count($model->issuesIds),
				])
			);

			return $this->redirect(Url::previous());
		}
		return $this->render('link-multiple', [
			'model' => $model,
		]);
	}

	/**
	 * Lists all IssueTag models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new TagSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueTag model.
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
	 * Creates a new IssueTag model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new IssueTag();
		$model->is_active = true;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueTag model.
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
	 * Deletes an existing IssueTag model.
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
	 * Finds the IssueTag model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueTag the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueTag {
		if (($model = IssueTag::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('issue', 'The requested page does not exist.'));
	}
}
