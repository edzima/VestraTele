<?php

namespace common\modules\lead\controllers;

use common\components\message\MessageTemplate;
use common\helpers\Flash;
use common\models\user\User;
use common\modules\lead\models\LeadMultipleSmsForm;
use common\modules\lead\models\LeadSmsForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class SmsController extends BaseController {

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'welcome' => ['POST'],
				],
			],
		];
	}

	public function actionPush(int $id, string $message = null) {
		$lead = $this->findLead($id);
		if (empty($lead->getPhone())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Try send sms to Lead: {lead} without Phone.', [
					'lead' => $lead->getName(),
				]));
			return $this->redirectLead($id);
		}

		$model = new LeadSmsForm($lead);
		if (!empty($message)) {
			$model->message = $message;
		}
		$model->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post())) {
			$jobId = $model->pushJob();
			if (!empty($jobId)) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success add SMS: {message} to send queue.', [
						'message' => $model->message,
					]));
				if ($model->getDelay()) {
					$model->delayReport($jobId);
				}
				return $this->redirectLead($lead->getId());
			}
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

	public function actionTemplate(int $id, string $key) {
		$lead = $this->findLead($id);
		if (empty($lead->getPhone())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Try send sms to Lead: {lead} without Phone.', [
					'lead' => $lead->getName(),
				]));
			return $this->redirect(['lead/view', 'id' => $id]);
		}

		/**
		 * @var User $user
		 */
		$user = Yii::$app->user->getIdentity();
		$phone = $user->getPhone();
		if (empty($phone)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'You has not set phone.', [
					'user' => $user->getFullName(),
				])
			);
			return $this->redirectLead($id);
		}

		$template = $this->findTemplate($key);

		$model = new LeadSmsForm($lead);
		$model->owner_id = $user->getId();
		$template->parseBody([
			'userName' => $user->profile->firstname,
			'userEmail' => $user->email,
			'userPhone' => Yii::$app->formatter->asTel($phone, [
				'asLink' => false,
			]),
		]);
		$model->message = $template->getSmsMessage();
		if (!Yii::$app->request->isPost) {
			return $this->redirect(['push', 'id' => $id, 'message' => $model->message]);
		}

		if (!empty($model->pushJob())) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success add SMS: {message} to send queue.', [
					'message' => $model->message,
				]));
		}

		return $this->redirectLead($lead->getId());
	}

	protected function findTemplate(string $key): MessageTemplate {
		$template = Yii::$app->messageTemplate->getTemplate($key);
		if ($template === null) {
			throw new NotFoundHttpException('Not Found Message Template for Key: ' . $key);
		}
		return $template;
	}
}
