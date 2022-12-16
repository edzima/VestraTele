<?php

namespace common\models\settlement;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayInterface;
use common\models\message\IssuePayPayedMessagesForm;
use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayPayedForm extends Model {

	public ?int $id = null;
	public ?string $date = null;
	public string $value;
	public string $transfer_type = TransferType::TRANSFER_TYPE_BANK;

	/**
	 * @var IssuePayInterface|IssuePay
	 */
	private IssuePayInterface $pay;
	private ?IssuePayPayedMessagesForm $_messagesForm = null;
	private ?IssuePayInterface $generatedPay = null;

	public function __construct(IssuePayInterface $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function init() {
		parent::init();
		$this->value = $this->pay->getValue()->toFixed(2);
	}

	public function rules(): array {
		return [
			[['value', 'transfer_type', 'date'], 'required'],
			['transfer_type', 'string'],
			['date', 'date', 'format' => 'Y-m-d'],
			['value', 'number', 'min' => 0.01],
			['value', 'compare', 'operator' => '<=', 'compareValue' => $this->pay->getValue(), 'enableClientValidation' => false],
			['transfer_type', 'in', 'range' => array_keys($this->getPay()::getTransfersTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'date' => Yii::t('settlement', 'Pay at'),
			'value' => Yii::t('settlement', 'Value'),
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
			$value = new Decimal($this->value);
			if (!$value->equals($pay->getValue())) {
				$this->divPay();
			}
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
		$message->isPartPayment = $this->wasDivider();
		return $message->pushMessages() > 0;
	}

	public function getMessagesModel(): IssuePayPayedMessagesForm {
		if ($this->_messagesForm === null) {
			$this->_messagesForm = new IssuePayPayedMessagesForm();
			$this->_messagesForm->sendSmsToCustomer = false;
			$this->_messagesForm->setPay($this->pay);
		}
		return $this->_messagesForm;
	}

	public function divPay(): ?IssuePay {
		if (!$this->validate(['value'])) {
			return null;
		}
		$value = new Decimal($this->value);
		$pay = $this->pay;
		if ($pay->getValue()->equals($value)) {
			return null;
		}
		$sub = $pay->getValue()->sub($value);
		$pay->value = $value->toFixed(2);
		if (!$pay->save(false)) {
			return null;
		}
		$newPay = new IssuePay();
		$newPay->calculation_id = $pay->getSettlementId();
		$newPay->value = $sub->toFixed(2);
		$newPay->deadline_at = $pay->deadline_at;
		$newPay->transfer_type = $pay->transfer_type;
		$newPay->vat = $pay->vat;
		$newPay->status = $pay->status;
		if (!$newPay->save(false)) {
			return null;
		}
		$this->generatedPay = $newPay;
		return $this->generatedPay;
	}

	public function wasDivider(): bool {
		return $this->generatedPay !== null;
	}

	public function getGeneratedPay(): ?IssuePayInterface {
		return $this->generatedPay;
	}

}
