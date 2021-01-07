<?php

namespace frontend\controllers;

use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\Summon;
use common\models\user\Worker;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CRUD for Summon model.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
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
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_NOTE],
					],
				],
			],
		];
	}

	/**
	 * Creates a new IssueNote model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionIssue(int $id) {
		$issue = Issue::findOne($id);
		if ($issue === null || !Yii::$app->user->canSeeIssue($issue)) {
			throw new NotFoundHttpException();
		}
		$note = new IssueNote();
		$note->issue_id = $issue->id;
		$note->user_id = Yii::$app->user->getId();

		$model = new IssueNoteForm($note);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirect(['/issue/view', 'id' => $issue->id]);
		}
		return $this->render('issue', [
			'model' => $model,
		]);
	}

	public function actionSettlement(int $id) {
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
			$this->redirect(['/settlement/view', 'id' => $settlement->id]);
		}
		return $this->render('settlement', [
			'model' => $model,
			'settlement' => $settlement,
		]);
	}

	public function actionSummon(int $id) {
		$summon = Summon::findOne($id);
		if ($summon === null || !Yii::$app->user->canSeeIssue($summon->getIssueModel())) {
			throw new NotFoundHttpException();
		}
		$note = new IssueNote();
		$note->issue_id = $summon->issue_id;
		$note->user_id = Yii::$app->user->id;
		$note->type = IssueNote::generateType(IssueNote::TYPE_SUMMON, $summon->id);
		$model = new IssueNoteForm($note);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirect(['/summon/view', 'id' => $summon->id]);
		}
		return $this->render('summon', [
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
		$model = new IssueNoteForm($this->findModel($id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirectIssue($model->getNote()->issue_id);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	private function redirectIssue(int $issueId) {
		return $this->redirect(['issue/view', 'id' => $issueId, '#' => 'notes-list']);
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
		if (($model = IssueNote::find()
				->andWhere([
					'id' => $id,
					'user_id' => Yii::$app->user->id,
				])
				->one()) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

}
