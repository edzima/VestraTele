<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\models\user\User;
use common\modules\lead\models\LeadMultipleSmsForm;
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
			return $this->redirectLead($id);
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

	public function actionPushMultiple(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('leadsIds');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING, 'Ids cannot be blank.');
			return $this->redirect(['lead/index']);
		}
		if (count($ids) === 1) {
			$id = reset($ids);
			return $this->redirect(['push', 'id' => $id]);
		}
		$ids = array_unique($ids);
		$model = new LeadMultipleSmsForm();
		$model->ids = $ids;
		$model->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && ($jobs = $model->pushJobs()) !== null) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success add: {count} SMS: {message} to send queue.', [
					'message' => $model->message,
					'count' => count($jobs),
				]));
			return $this->redirect(['lead/index']);
		}
		return $this->render('push-mutliple', [
			'model' => $model,
		]);
	}

	public function actionWelcome(int $id) {
		$lead = $this->findLead($id);
		if (empty($lead->getPhone())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Try send sms to Lead: {lead} without Phone.', [
					'lead' => $lead->getName(),
				]));
			return $this->redirect(['lead/view', 'id' => $id]);
		}
		$template = Yii::$app->messageTemplate->getTemplate('lead.sms.welcome');
		if ($template === null) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Not Found Message template for Welcome SMS.')
			);
			return $this->redirect(['/message-template/default']);
		}
		/**
		 * @var User $user
		 */
		$user = Yii::$app->user->getIdentity();
		$phone = $user->getPhone();
		if (empty($phone)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'User: {user} has not set phone.', [
					'user' => $user->getFullName(),
				])
			);
			return $this->redirectLead($id);
		}
		$model = new LeadSmsForm($lead);
		$model->owner_id = $user->getId();
		$model->message = $template->getBody();
		$template->parseBody([
			'userName' => $user->profile->firstname,
			'userPhone' => Yii::$app->formatter->asTel('', [
				'asLink' => false,
			]),
		]);

		return $this->redirectLead($id);
	}
}
