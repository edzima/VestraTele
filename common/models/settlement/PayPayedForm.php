<?php

namespace common\models\settlement;

use common\components\message\IssueSettlementMessageFactory;
use common\models\issue\IssuePayInterface;
use common\models\issue\IssueUser;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayPayedForm extends Model {

	public ?int $id = null;
	public ?string $date = null;
	public string $transfer_type = TransferType::TRANSFER_TYPE_BANK;
	public bool $sendEmailToCustomer = true;
	public bool $sendEmailToWorkers = true;

	private ?IssueSettlementMessageFactory $_messageFactory = null;
	private IssuePayInterface $pay;
	public bool $sendSmsToCustomer;

	public function __construct(IssuePayInterface $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function init(): void {
		parent::init();
		$this->sendSmsToCustomer = $this->getMessageFactory()->customerHasPhones();
		$this->sendEmailToCustomer = $this->getMessageFactory()->customerHasEmail();
	}

	public function rules(): array {
		return [
			[['transfer_type', 'date'], 'required'],
			['transfer_type', 'string'],
			['date', 'date', 'format' => 'Y-m-d'],
			[['sendSmsToCustomer', 'sendEmailToCustomer', 'sendEmailToWorkers'], 'boolean'],
			['transfer_type', 'in', 'range' => array_keys($this->getPay()::getTransfersTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'sendEmailToCustomer' => Yii::t('settlement', 'Send Email to Customer'),
			'sendSmsToCustomer' => Yii::t('settlement', 'Send SMS to Customer'),
			'sendEmailToWorkers' => Yii::t('settlement', 'Send Email to Workers'),
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'date' => Yii::t('settlement', 'Pay at'),
		];
	}

	public function getPay(): IssuePayInterface {
		return $this->pay;
	}

	public function pay(): bool {
		if ($this->validate()) {
			$pay = $this->getPay();
			/** @noinspection PhpUnhandledExceptionInspection */
			return $pay->markAsPaid(new DateTime($this->date), $this->transfer_type);
		}
		return false;
	}

	public function getMessageFactory(): IssueSettlementMessageFactory {
		if ($this->_messageFactory === null) {
			$this->_messageFactory = new IssueSettlementMessageFactory();
			$this->_messageFactory->issue = $this->pay->calculation;
		}
		return $this->_messageFactory;
	}

	/**
	 * @return bool
	 */
	public function sendEmailToCustomer(): bool {
		$message = $this->getMessageFactory()->getEmailAboutPayedPayToCustomer($this->pay);
		if (!$message) {
			return false;
		}
		return $message->send();
	}

	public function sendSmsToCustomer(int $user_id): bool {
		$sms = $this->getMessageFactory()->getSmsAboutPayedPayToCustomer($this->pay, $user_id);
		if ($sms === null) {
			return false;
		}

		return !empty($sms->pushJob());
	}

	/**
	 * @param array $types
	 * @return int
	 */
	public function sendEmailsToWorkers(array $types = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER]): bool {
		$message = $this->getMessageFactory()->getEmailAboutPayedPayToWorkers($this->pay, $types);
		if (!$message) {
			return false;
		}
		return $message->send();
	}
}
