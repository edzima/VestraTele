<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueNoteForm;
use backend\modules\issue\models\search\IssueNoteSearch;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\Summon;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NoteController implements the CRUD actions for IssueNote model.
 */
class NoteController extends Controller {

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
	 * Creates a new IssueNote model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $issueId
	 * @param string|null $type
	 * @return mixed
	 * @throws NotFoundHttpException
	 * @throws InvalidConfigException
	 * @todo add test for them
	 */
	public function actionCreate(int $issueId, string $type = null) {
		if ($type !== null && !isset(IssueNote::getTypesNames()[$type])) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		$note = new IssueNote();
		$note->issue_id = $this->findIssueModel($issueId)->id;
		$note->user_id = Yii::$app->user->id;
		$note->type = $type;

		$model = new IssueNoteForm($note);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($type === IssueNote::TYPE_SETTLEMENT) {
				return $this->redirectPayCalculation($issueId);
			}
			return $this->redirectIssue($issueId);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateSettlement(int $id) {
		$settlement = IssuePayCalculation::findOne($id);
		if ($settlement === null || !Yii::$app->user->canSeeIssue($settlement->getIssueModel())) {
			throw new NotFoundHttpException();
		}
		$note = new IssueNote();
		$note->issue_id = $settlement->issue_id;
		$note->user_id = Yii::$app->user->getId();
		$note->type = IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, $settlement->id);
		$model = new IssueNoteForm($note);

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
		$note = new IssueNote();
		$note->issue_id = $summon->issue_id;
		$note->user_id = Yii::$app->user->id;
		$note->type = IssueNote::generateType(IssueNote::TYPE_SUMMON, $summon->id);
		$model = new IssueNoteForm($note);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirectIssue($summon->issue_id);
		}
		return $this->render('create', [
			'model' => $model,
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
		$model = new IssueNoteForm($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->note->isForSettlement()) {
				return $this->redirect(['/settlement/calculation/view', 'id' => $model->note->getEntityId()]);
			}
			return $this->redirectIssue($model->getNote()->issue_id);
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
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$issueId = $model->issue_id;
		$model->delete();
		Yii::warning('User: ' . Yii::$app->user->id . ' delete note. Title: ' . $model->title . "\n description: " . $model->description, 'note.delete');
		return $this->redirectIssue($issueId);
	}

	private function redirectPayCalculation(int $issueId) {
		return $this->redirect(['/settlement/calculation/pay-calculation/view', 'id' => $issueId]);
	}

	private function redirectIssue(int $issueId) {
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
	protected function findModel($id): IssueNote {
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
