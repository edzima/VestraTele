<?php

namespace backend\modules\settlement\models;

use common\components\message\IssueSettlementMessageFactory;
use common\models\issue\IssueUser;
use common\models\settlement\CalculationForm as BaseCalculationForm;
use Yii;

class CalculationForm extends BaseCalculationForm {

	public bool $sendEmailToCustomer = true;
	public bool $sendEmailToWorkers = true;
	public bool $sendSmsToCustomer = true;
	public bool $sendSmsToAgent = true;

	private ?IssueSettlementMessageFactory $_messageFactory = null;

	public function rules(): array {
		return array_merge(
			parent::rules(), [
			[
				[
					'sendEmailToWorkers', 'sendEmailToCustomer',
					'sendSmsToCustomer', 'sendSmsToAgent',
				], 'boolean', 'on' => static::SCENARIO_CREATE,
			],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
			'sendEmailToCustomer' => Yii::t('settlement', 'Send Email to Customer'),
			'sendEmailToWorkers' => Yii::t('settlement', 'Send Email to Workers'),
			'sendSmsToCustomer' => Yii::t('settlement', 'Send SMS to Customer'),
			'sendSmsToAgent' => Yii::t('settlement', 'Send SMS to Agent'),
		]);
	}

	public function init() {
		parent::init();
		if ($this->isCreateScenario()) {
			$this->sendEmailToCustomer = !empty($this->getIssue()->customer->email);
			$this->sendSmsToCustomer = $this->getIssue()->customer->profile->hasPhones();
			$this->sendSmsToAgent = $this->getIssue()->agent->profile->hasPhones();
		}
	}

	protected function getMessageFactory(): IssueSettlementMessageFactory {
		if ($this->_messageFactory === null) {
			$this->_messageFactory = new IssueSettlementMessageFactory();
		}
		return $this->_messageFactory;
	}

	public function sendAboutCreateMessages(): void {
		if ($this->sendEmailToCustomer) {
			$this->sendEmailAboutCreateToCustomer();
		}
		if ($this->sendSmsToCustomer) {
			$this->sendSmsAboutCreateToCustomer();
		}
		if ($this->sendEmailToWorkers) {
			$this->sendEmailAboutCreateToWorker();
		}
		if ($this->sendSmsToAgent) {
			$this->sendSmsAboutCreateToAgent();
		}
	}

	public function sendSmsAboutCreateToCustomer(): bool {
		if (!$this->isCreateScenario()) {
			return false;
		}
		$sms = $this->getMessageFactory()
			->getSmsAboutCreateSettlementToCustomer($this->getModel());
		if ($sms === null) {
			return false;
		}
		return !empty($sms->pushJob());
	}

	public function sendSmsAboutCreateToAgent(): bool {
		if (!$this->isCreateScenario()) {
			return false;
		}
		$sms = $this->getMessageFactory()
			->getSmsAboutCreateSettlementToAgent($this->getModel());
		if ($sms === null) {
			return false;
		}
		return !empty($sms->pushJob());
	}

	public function sendEmailAboutCreateToCustomer(): bool {
		if (!$this->isCreateScenario()) {
			return false;
		}
		$message = $this->getMessageFactory()
			->getEmailAboutCreateSettlementToCustomer($this->getModel());
		if ($message === null) {
			return false;
		}
		return $message->send();
	}

	public function sendEmailAboutCreateToWorker(array $types = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER]): bool {
		if (!$this->isCreateScenario()) {
			return false;
		}
		$message = $this->getMessageFactory()
			->getEmailAboutCreateSettlementToWorkers($this->getModel(), $types);
		if ($message === null) {
			return false;
		}
		return $message->send();
	}

}
