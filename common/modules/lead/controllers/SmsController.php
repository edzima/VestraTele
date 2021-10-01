<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\LeadSmsForm;
use Yii;

class SmsController extends BaseController {

	public function actionPush(int $id) {
		$lead = $this->findLead($id);
		if (empty($lead->getPhone())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Try send sms to Lead: {lead} without Phone.', [
					'lead' => $lead->getName(),
				]));
			return $this->redirect(['lead/index']);
		}

		$model = new LeadSmsForm($lead);
		$model->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && !empty($model->pushJob())) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success add SMS: {message} to send queue.', [
					'message' => $model->message,
				]));
			return $this->redirectLead($lead->getId());
		}
		return $this->render('push', [
			'model' => $model,
		]);
	}
}
