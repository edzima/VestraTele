<?php

namespace common\modules\reminder\controllers;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadReminder;
use common\modules\reminder\models\ReminderForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class LeadController extends Controller {

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
			return $this->redirect(['/lead/lead/view', 'id' => $lead->getId()]);
		}
		return $this->render('create', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	private function findLead(int $id): ActiveLead {
		$model = Yii::$app->leadManager->findById($id);
		if ($model) {
			return $model;
		}
		throw new NotFoundHttpException();
	}
}
