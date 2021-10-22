<?php

namespace common\models\settlement;

use common\models\issue\IssuePayInterface;
use common\models\message\IssuePayPayedMessagesForm;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayPayedForm extends Model {

	public ?int $id = null;
	public ?string $date = null;
	public string $transfer_type = TransferType::TRANSFER_TYPE_BANK;

	private IssuePayInterface $pay;
	private ?IssuePayPayedMessagesForm $_messagesForm = null;

	public function __construct(IssuePayInterface $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['transfer_type', 'date'], 'required'],
			['transfer_type', 'string'],
			['date', 'date', 'format' => 'Y-m-d'],
			['transfer_type', 'in', 'range' => array_keys($this->getPay()::getTransfersTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'date' => Yii::t('settlement', 'Pay at'),
		];
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName) && $this->getMessagesModel()->load($data, $formName);
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

	public function pushMessages(int $smsOwnerId): bool {
		if (!$this->pay->isPayed()) {
			return false;
		}
		$message = $this->getMessagesModel();
		$message->sms_owner_id = $smsOwnerId;
		return $message->pushMessages() > 0;
	}

	public function getMessagesModel(): IssuePayPayedMessagesForm {
		if ($this->_messagesForm === null) {
			$this->_messagesForm = new IssuePayPayedMessagesForm();
			$this->_messagesForm->setPay($this->pay);
		}
		return $this->_messagesForm;
	}

}
