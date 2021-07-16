<?php

namespace frontend\controllers;

use common\models\user\User;
use common\modules\lead\components\LeadManager;
use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\forms\CzaterLeadForm;
use common\modules\lead\models\forms\LandingLeadForm;
use common\modules\lead\models\forms\LeadPushEmail;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\rest\Controller;

class ApiLeadController extends Controller {

	public function init() {
		Module::manager()->on(LeadManager::EVENT_AFTER_PUSH, [$this, 'afterPush']);
		parent::init();
	}

	public function actionLanding() {

		$model = new LandingLeadForm();
		$model->date_at = date($model->dateFormat);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $this->pushLead($model)) {
				return [
					'status' => 'success',
				];
			}
			Yii::warning([
				'message' => 'Landing lead with validate errors.',
				'post' => Yii::$app->request->post(),
				'error' => $model->getErrors(),
			], 'lead.landing.error');

			return [
				'status' => 'error',
				'errors' => $model->getErrors(),
			];
		}
		Yii::warning([
			'message' => 'Landing Lead not Loaded Data',
			'post' => Yii::$app->request->post(),
			'error' => $model->getErrors(),
		], 'lead.landing.error');
		return [
			'status' => 'warning',
			'message' => 'Not Send Data',
		];
	}

	public function actionCzater() {
		Yii::warning([
			'headers' => Yii::$app->request->headers->toArray(),
			'message' => Yii::$app->request->post(),
		], 'lead.czater');

		//musimy pobrac szczegoly rozmowy, pozniej pobrac id konsultana i tam mamy zrodlo
		$model = new CzaterLeadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $this->pushLead($model)) {
				return [
					'status' => 'success',
				];
			}

			Yii::warning([
				'message' => 'Czater lead with validate errors.',
				'post' => Yii::$app->request->post(),
				'error' => $model->getErrors(),
			], 'lead.landing.error');

			return [
				'status' => 'error',
				'errors' => $model->getErrors(),
			];
		}
		return [
			'status' => 'warning',
			'message' => 'Not Send Data',
		];
	}

	protected function afterPush(LeadEvent $event): void {
		$lead = $event->getLead();
		$ownerId = $lead->getUsers()[LeadUser::TYPE_OWNER] ?? null;
		$model = new LeadPushEmail($lead);
		if ($ownerId) {
			$model->email = User::findOne($ownerId)->email;
		} else {
			if (!isset(Yii::$app->params['leads.emailWithoutOwner'])) {
				throw new InvalidConfigException('Param with key -> leads.emailWithoutOwner must be set');
			}
			$model->email = Yii::$app->params['leads.emailWithoutOwner'];
		}
		$model->sendEmail();
	}

	protected function pushLead(LeadInterface $lead): bool {
		return Module::manager()->pushLead($lead) !== null;
	}
}
