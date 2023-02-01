<?php

namespace frontend\controllers;

use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\Summon;
use common\models\message\IssueNoteMessagesForm;
use common\models\message\SummonNoteMessagesForm;
use common\models\message\UpdateNoteMessagesForm;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\issue\actions\NoteDescriptionListAction;
use common\modules\issue\actions\NoteTitleListAction;
use frontend\models\IssueNoteForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CRUD for Summon model.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
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
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_NOTE, Worker::PERMISSION_NOTE_SELF],
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
		$issue = $this->findIssue($id);

		$model = new IssueNoteForm([
			'issue_id' => $issue->getIssueId(),
			'user_id' => Yii::$app->user->getId(),
		]);

		$message = new IssueNoteMessagesForm([
			'issue' => $issue,
			'sms_owner_id' => Yii::$app->user->getId(),
			'hiddenFields' => [
				'sendSmsToCustomer',
				'sendEmailToCustomer',
			],
			'sendEmailToWorkers' => true,
		]);
		$message->setExtraWorkersEmailsIds(Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_ISSUE));
		$model->messagesForm = $message;

		if (!Yii::$app->user->can(User::PERMISSION_NOTE)) {
			$model->type = IssueNote::TYPE_SELF;
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('frontend', 'Warning! This note will only be visible to you.'));
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirect(['/issue/view', 'id' => $issue->getIssueId()]);
		}
		return $this->render('issue', [
			'model' => $model,
			'issue' => $issue,
		]);
	}

	public function actionSettlement(int $id) {
		$settlement = IssuePayCalculation::findOne($id);
		if ($settlement === null || !Yii::$app->user->canSeeIssue($settlement)) {
			throw new NotFoundHttpException();
		}
		$model = IssueNoteForm::createSettlement($settlement);
		$model->user_id = Yii::$app->user->getId();
		$message = new IssueNoteMessagesForm([
			'issue' => $settlement->issue,
			'sms_owner_id' => Yii::$app->user->getId(),
			'hiddenFields' => [
				'sendSmsToCustomer',
				'sendEmailToCustomer',
			],
			'sendEmailToWorkers' => true,
		]);
		$message->setExtraWorkersEmailsIds(Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SETTLEMENT));
		$model->messagesForm = $message;

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
		if ($summon === null || !Yii::$app->user->canSeeIssue($summon)) {
			throw new NotFoundHttpException();
		}
		$model = IssueNoteForm::createSummon($summon);
		$model->user_id = Yii::$app->user->getId();
		$message = new SummonNoteMessagesForm([
			'summon' => $summon,
			'sms_owner_id' => Yii::$app->user->getId(),
			'hiddenFields' => [
				'sendSmsToCustomer',
				'sendEmailToCustomer',
			],
			'sendEmailToWorkers' => true,
		]);
		$message->setExtraWorkersEmailsIds(Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SUMMON));
		$model->messagesForm = $message;
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
		$note = $this->findModel($id);
		if ($note->isSms()) {
			throw new NotFoundHttpException('Cannot update SMS Note.');
		}
		if (
			$note->user_id !== Yii::$app->user->getId() && !Yii::$app->user->can(User::PERMISSION_NOTE_UPDATE)) {
			throw new ForbiddenHttpException('Only self note can update or User with Note Update permission.');
		}
		$model = new IssueNoteForm();
		$model->updater_id = Yii::$app->user->getId();
		$model->setModel($note);
		$message = new UpdateNoteMessagesForm([
			'note' => $note,
			'sms_owner_id' => Yii::$app->user->getId(),
			'hiddenFields' => [
				'sendSmsToCustomer',
				'sendEmailToCustomer',
			],
			'sendEmailToWorkers' => true,
		]);
		$model->messagesForm = $message;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirectIssue($note->issue_id);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionDelete(int $id): Response {
		$model = $this->findModel($id);
		if (!Yii::$app->user->canDeleteNote($model)) {
			Yii::warning('User: ' . Yii::$app->user->getId() . ' try Delete Note #:' . $model->id . ' with description: ' . $model->description);
			throw new ForbiddenHttpException();
		}
		$model->delete();
		Yii::warning('User: ' . Yii::$app->user->id . ' delete note. Title: ' . $model->title . "\n description: " . $model->description, 'note.delete');
		return $this->redirectIssue($model->issue_id);
	}

	private function redirectIssue(int $issueId): Response {
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

	protected function findIssue(int $id): Issue {
		$issue = Issue::findOne($id);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}
		if (!Yii::$app->user->canSeeIssue($issue)) {
			throw new MethodNotAllowedHttpException();
		}
		return $issue;
	}
}
