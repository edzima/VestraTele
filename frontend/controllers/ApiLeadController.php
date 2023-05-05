<?php

namespace frontend\controllers;

use common\models\KeyStorageItem;
use common\models\user\User;
use common\modules\lead\components\LeadManager;
use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\CzaterLeadForm;
use common\modules\lead\models\forms\LandingLeadForm;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\LeadPushEmail;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use Edzima\Yii2Adescom\exceptions\Exception;
use SoapFault;
use Yii;
use yii\base\InvalidConfigException;
use yii\rest\Controller;

class ApiLeadController extends Controller {

	public function init() {
		Module::manager()->on(LeadManager::EVENT_AFTER_PUSH, [$this, 'afterPush']);
		parent::init();
	}

	public function actionLanding(string $region = null) {
		if ($region === null) {
			$region = Yii::$app->formatter->defaultPhoneRegion;
		}

		$model = new LandingLeadForm();
		$model->date_at = date($model->dateFormat);
		$model->phoneRegion = $region;

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

	public function actionZapier() {
		$model = new LeadForm();
		Yii::warning([
			'post' => Yii::$app->request->post(),
			'bodyParams' => Yii::$app->request->bodyParams,
			'queryParams' => Yii::$app->request->queryParams,
			'header' => Yii::$app->request->headers->toArray(),
		], __METHOD__);
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate()) {
				return [
					'success' => true,
				];
			}
			return $model->getErrors();
		}
		return [
			'message' => 'Not send Data',
		];
	}

	protected function afterPush(LeadEvent $event): void {
		$lead = $event->getLead();
		$this->sendEmail($lead);
		$this->sendSms($lead);
	}

	private function sendEmail(LeadInterface $lead): void {
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

	private function sendSms(ActiveLead $lead): void {
		if (!empty($lead->getPhone()) && !empty($lead->getSource()->getPhone())) {
			$message = Yii::t('lead', "Thank you for submitting your application.\n"
				. "We will contact you within 24 hours.\n"
				. "If you do not want to wait, call us directly on the number: {sourcePhone}", [
				'sourcePhone' => $lead->getSource()->getPhone(),
			]);
			$model = new LeadSmsForm($lead);
			$model->message = $message;
			$model->owner_id = $this->getSmsOwnerId();
			try {
				$model->send();
			} catch (Exception $exception) {
				Yii::error($exception->getMessage(), 'lead.api.sendSms');
			} catch (SoapFault $exception) {
				Yii::error($exception->getMessage(), 'lead.api.sendSms.soap');
			}
		}
	}

	private function getSmsOwnerId(): int {
		$owner = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID);
		if ($owner === null) {
			throw new InvalidConfigException('Not Set Robot SMS Owner. Key: (' . KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID . ').');
		}
		return $owner;
	}

	protected function pushLead(LeadInterface $lead): bool {
		return Module::manager()->pushLead($lead) !== null;
	}
}
