<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueNoteForm;
use backend\modules\issue\models\search\IssueNoteSearch;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\Summon;
use common\modules\issue\actions\NoteDescriptionListAction;
use common\modules\issue\actions\NoteTitleListAction;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * NoteController implements the CRUD actions for IssueNote model.
 */
class NoteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function actions(): array {
		return [
			'title-list' => NoteTitleListAction::class,
			'description-list' => NoteDescriptionListAction::class,
		];
	}

	/**
	 * @inheritdoc
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
	 * Lists all IssueNote models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueNoteSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueNote model.
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
	 * Creates a new IssueNote model for Issue.
	 * If creation is successful, the browser will be redirected to the Issue 'view' page.
	 *
	 * @param int $issueId
	 * @param string|null $type
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $issueId) {
		$issue = $this->findIssueModel($issueId);
		$model = new IssueNoteForm([
			'issue_id' => $issue->getIssueId(),
			'user_id' => Yii::$app->user->id,
		]);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirectIssue($issueId);
		}
		return $this->render('create', [
			'model' => $model,
			'issue' => $issue,
		]);
	}

	public function actionCreateSettlement(int $id) {
		$settlement = IssuePayCalculation::findOne($id);
		if ($settlement === null) {
			throw new NotFoundHttpException();
		}
		$model = IssueNoteForm::createSettlement($settlement);
		$model->user_id = Yii::$app->user->getId();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/settlement/calculation/view', 'id' => $settlement->id]);
		}
		return $this->render('create-settlement', [
			'model' => $model,
			'settlement' => $settlement,
		]);
	}

	public function actionCreateSummon(int $id) {
		$summon = Summon::findOne($id);
		if ($summon === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		$model = IssueNoteForm::createSummon($summon);
		$model->user_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirectIssue($summon->issue_id);
		}
		return $this->render('create-summon', [
			'model' => $model,
			'summon' => $summon,
		]);
	}

	/**
	 * Updates an existing IssueNote model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$model = new IssueNoteForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->getModel()->isForSettlement()) {
				return $this->redirect(['/settlement/calculation/view', 'id' => $model->getModel()->getEntityId()]);
			}
			return $this->redirectIssue($model->getModel()->getIssueId());
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueNote model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(int $id): Response {
		$model = $this->findModel($id);
		$model->delete();
		Yii::warning('User: ' . Yii::$app->user->id . ' delete note. Title: ' . $model->title . "\n description: " . $model->description, 'note.delete');
		return $this->redirectIssue($model->issue_id);
	}

	private function redirectIssue(int $issueId): Response {
		return $this->redirect(['issue/view', 'id' => $issueId]);
	}

	/**
	 * Finds the IssueNote model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueNote the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueNote {
		if (($model = IssueNote::findOne($id)) !== null) {
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
