<?php

namespace frontend\controllers;

use common\modules\lead\models\forms\CzaterLeadForm;
use common\modules\lead\models\forms\LandingLeadForm;
use Yii;
use yii\rest\Controller;

class LeadController extends Controller {

	public function actionLanding() {
		$model = new LandingLeadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate()) {
				$lead = Yii::$app->leadManager->pushLead($model);
			} else {
				Yii::warning([
					'message' => 'Landing lead with validate errors.',
					'post' => Yii::$app->request->post(),
					'error' => $model->getErrors(),
				], 'lead.landing.error');
			}
		}
	}

	public function actionCzater() {
		Yii::warning([
			'headers' => Yii::$app->request->headers->toArray(),
			'message' => Yii::$app->request->post(),
		], 'lead.czater');

		//musimy pobrac szczegoly rozmowy, pozniej pobrac id konsultana i tam mamy zrodlo
		$model = new CzaterLeadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate()) {
				$lead = Yii::$app->leadManager->pushLead($model);
			} else {
				Yii::warning([
					'message' => 'Czater lead with validate errors.',
					'post' => Yii::$app->request->post(),
					'error' => $model->getErrors(),
				], 'lead.landing.error');
			}
		}
		return '';
	}
}
