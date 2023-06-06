<?php

namespace common\modules\lead\controllers;

use common\helpers\Url;
use common\modules\lead\models\forms\LeadReminderForm;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\searches\LeadReminderSearch;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ReminderController extends BaseController {

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'done' => ['POST'],
					'not-done' => ['POST'],
				],
			],
		];
	}

	public ?bool $allowDelete = true;

	public function actionIndex() {
		$searchModel = new LeadReminderSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->leadUserId = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate(int $id) {
		$lead = $this->findLead($id);
		$model = new LeadReminderForm();
		$model->setLead($this->findLead($id));
		if ($this->module->onlyUser) {
			$model->user_id = Yii::$app->user->getId();
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['lead/view', 'id' => $id]);
		}
		return $this->render('create', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	public function actionView(int $lead_id) {
		return $this->redirectLead($lead_id);
	}

	public function actionUpdate(int $lead_id, int $reminder_id) {
		$leadReminder = $this->findModel($lead_id, $reminder_id);
		if ($leadReminder->reminder->user_id !== null && $leadReminder->reminder->user_id !== Yii::$app->user->getId()) {
			throw new ForbiddenHttpException(
				Yii::t('lead',
					'Only General or Self Reminder can be updated.'
				)
			);
		}
		$model = new LeadReminderForm();
		$model->setLeadReminder($leadReminder);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['lead/view', 'id' => $lead_id]);
		}
		return $this->render('update', [
			'model' => $model,
			'lead' => $leadReminder->lead,
		]);
	}

	public function actionDelete(int $lead_id, int $reminder_id) {
		$model = $this->findModel($lead_id, $reminder_id);
		$model->delete();
		$model->reminder->delete();
		return $this->redirect(['lead/view', 'id' => $lead_id]);
	}

	public function actionDone(int $lead_id, int $reminder_id, string $returnUrl = null) {
		$model = $this->findModel($lead_id, $reminder_id);
		if (!$model->reminder->isDone()) {
			$model->reminder->markAsDone();
			$model->reminder->save();
		}
		return $this->redirect($returnUrl ?: $this->redirectLead($lead_id));
	}

	public function actionNotDone(int $lead_id, int $reminder_id, string $returnUrl = null) {
		$model = $this->findModel($lead_id, $reminder_id);
		if ($model->reminder->isDone()) {
			$model->reminder->unmarkAsDone();
			$model->reminder->save();
		}
		return $this->redirect($returnUrl ?: $this->redirectLead($lead_id));
	}

	/**
	 * @throws NotFoundHttpException
	 * @throws ForbiddenHttpException
	 */
	private function findModel(int $lead_id, int $reminder_id): LeadReminder {
		$model = LeadReminder::find()
			->andWhere([
				'lead_id' => $lead_id,
				'reminder_id' => $reminder_id,
			])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		/** @var LeadReminder $model */
		if (!$this->module->manager->isForUser($model->lead)) {
			throw new ForbiddenHttpException();
		}
		return $model;
	}
}
