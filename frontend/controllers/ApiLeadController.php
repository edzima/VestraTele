<?php

namespace frontend\controllers;

use common\components\callpage\CallPageClient;
use common\models\KeyStorageItem;
use common\models\user\User;
use common\modules\lead\components\LeadManager;
use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\CustomerLeadForm;
use common\modules\lead\models\forms\CzaterLeadForm;
use common\modules\lead\models\forms\LandingLeadForm;
use common\modules\lead\models\forms\LeadPushEmail;
use common\modules\lead\models\forms\MessageZapierLeadForm;
use common\modules\lead\models\forms\ZapierLeadForm;
use common\modules\lead\models\Lead;
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

	/**
	 * {@inheritdoc}
	 */
	public function verbs() {
		return [
			'customer' => ['POST'],
			'landing' => ['POST'],
			'zapier' => ['POST'],
			'message-zapier' => ['POST'],
		];
	}

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
		$model = new ZapierLeadForm();
		$model->date_at = date($model->dateFormat);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $this->pushLead($model)) {
				return [
					'status' => 'success',
				];
			}
			Yii::warning([
				'message' => 'Zapier lead with validate errors.',
				'post' => Yii::$app->request->post(),
				'error' => $model->getErrors(),
			], 'lead.zapier.error');

			return [
				'status' => 'error',
				'errors' => $model->getErrors(),
			];
		}
		Yii::warning([
			'message' => 'Zapier Lead not Loaded Data',
			'post' => Yii::$app->request->post(),
			'error' => $model->getErrors(),
		], 'lead.zapier.error');
		return [
			'status' => 'warning',
			'message' => 'Not Send Data',
		];
	}

	public function actionMessageZapier() {
		$model = new MessageZapierLeadForm();
		$model->date_at = date($model->dateFormat);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $this->pushLead($model)) {
				return [
					'status' => 'success',
				];
			}
			Yii::warning([
				'message' => 'Message Zapier lead with validate errors.',
				'post' => Yii::$app->request->post(),
				'error' => $model->getErrors(),
			], 'lead.message-zapier.error');

			return [
				'status' => 'error',
				'errors' => $model->getErrors(),
			];
		}
		Yii::warning([
			'message' => 'Landing Lead not Loaded Data',
			'post' => Yii::$app->request->post(),
			'error' => $model->getErrors(),
		], 'lead.message-zapier.error');
		return [
			'status' => 'warning',
			'message' => 'Not Send Data',
		];
	}

	public function actionCustomer(): array {
		$model = new CustomerLeadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $this->pushLead($model)) {
				return [
					'success' => true,
				];
			}
			Yii::warning([
				'message' => 'Customer lead with validate errors.',
				'post' => Yii::$app->request->post(),
				'error' => $model->getErrors(),
			], 'lead.landing.error');
			return $model->getErrors();
		}
		return [
			'success' => false,
			'message' => 'Not send Data',
		];
	}

	protected function afterPush(LeadEvent $event): void {
		$lead = $event->getLead();
		$this->sendEmail($lead);
		$this->sendSms($lead);
		$this->pushCall($lead);
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
		if ($this->smsShouldSend($lead)) {
			$message = Yii::t('lead', $lead->getSource()->getSmsPushTemplate(), [
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

	private function getSmsOwnerId(): ?int {
		$owner = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID);
		if ($owner === null) {
			Yii::warning('Not Set Robot SMS Owner. Key: (' . KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID . ').');
		}
		return $owner;
	}

	protected function pushLead(LeadInterface $lead): bool {
		return Module::manager()->pushLead($lead) !== null;
	}

	private function smsShouldSend(ActiveLead $lead): bool {
		return !empty($lead->getPhone())
			&& !empty($lead->getSource()->getSmsPushTemplate())
			&& !empty($lead->getSource()->getPhone())
			&& $lead->getProvider() !== Lead::PROVIDER_CRM_CUSTOMER
			&& $this->getSmsOwnerId() !== null;
	}

	private function pushCall(LeadInterface $lead): bool {
		if (empty($lead->getPhone())) {
			return false;
		}
		if (empty($lead->getSource()->getCallPageWidgetId())) {
			return false;
		}

		$callPage = Yii::$app->get('callPageClient', false);
		if ($callPage === null) {
			return false;
		}
		/** @var CallPageClient $callPage */
		return $callPage->simpleCall($lead->getSource()->getCallPageWidgetId(), $lead->getPhone());
	}
}
