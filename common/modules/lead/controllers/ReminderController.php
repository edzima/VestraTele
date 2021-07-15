<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\reminder\models\ReminderForm;
use common\modules\lead\models\searches\LeadReminderSearch;
use Yii;
use yii\web\NotFoundHttpException;

class ReminderController extends BaseController {

	public function actionIndex() {
		$searchModel = new LeadReminderSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate(int $id) {
		$lead = $this->findLead($id);
		$model = new ReminderForm();
		$model->date_at = date(DATE_ATOM, strtotime("+1 week"));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$leadReminder = new LeadReminder([
				'lead_id' => $lead->getId(),
				'reminder_id' => $model->getModel()->id,
			]);
			$leadReminder->save();
			return $this->redirect(['lead/view', 'id' => $id]);
		}
		return $this->render('create', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	public function actionUpdate(int $lead_id, int $reminder_id) {
		$leadReminder = $this->findModel($lead_id, $reminder_id);
		$model = new ReminderForm();
		$model->setModel($leadReminder->reminder);
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
		return $model;
	}
}
