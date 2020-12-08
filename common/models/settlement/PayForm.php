<?php

namespace common\models\settlement;

use common\models\issue\IssuePay;
use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\Model;

class PayForm extends Model implements PayInterface {

	public const DEADLINE_INTERVAL_3_DAYS = '+ 3 days';
	public const DEADLINE_INTERVAL_5_DAYS = '+ 5 days';
	public const DEADLINE_INTERVAL_7_DAYS = '+ 7 days';
	public const DEADLINE_INTERVAL_14_DAYS = '+ 14 days';
	public const DEADLINE_INTERVAL_30_DAYS = '+ 30 days';

	public ?string $deadlineInterval = null;

	public string $value = '';
	public ?string $vat = null;
	public ?string $payment_at = null;
	public ?string $deadline_at = null;
	public int $transferType = IssuePay::TRANSFER_TYPE_BANK;

	public string $dateFormat = 'Y-m-d';

	public function rules(): array {
		return [
			[['value', 'transferType'], 'required'],
			[['transferType'], 'integer'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			['value', 'number', 'min' => 1],
			['value', 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/', 'enableClientValidation' => false],
			[['deadline_at', 'payment_at'], 'date', 'format' => $this->dateFormat],
			[
				'payment_at', 'required', 'enableClientValidation' => false, 'when' => function () {
				return empty($this->deadlineInterval) && empty($this->deadline_at);
			}, 'message' => Yii::t('settlement', '{attribute} is required when {secondLabel} is empty.', ['secondLabel' => $this->getAttributeLabel('deadlineAt')]),
			],
			[
				'deadline_at', 'required', 'enableClientValidation' => false, 'when' => function () {
				return empty($this->deadlineInterval) && empty($this->payment_at);
			}, 'message' => Yii::t('settlement', '{attribute} is required when {secondLabel} is empty.', ['secondLabel' => $this->getAttributeLabel('paymentAt')]),
			],
			['transferType', 'in', 'range' => array_keys(static::getTransferTypesNames())],
			['deadlineInterval', 'in', 'range' => array_keys(static::getDeadlineIntervals())],
		];
	}

	public function attributeLabels(): array {
		return [
			'deadline_at' => Yii::t('settlement', 'Deadline at'),
			'deadlineAt' => Yii::t('settlement', 'Deadline at'),
			'transferType' => Yii::t('settlement', 'Pay transfer type'),
			'value' => Yii::t('settlement', 'Value with VAT'),
			'payment_at' => Yii::t('settlement', 'Payment at'),
			'paymentAt' => Yii::t('settlement', 'Payment at'),
		];
	}

	public function setPay(PayInterface $pay): void {
		$this->value = $pay->getValue()->toFixed(2);
		$this->vat = $pay->getVAT() ? $pay->getVAT()->toFixed(2) : null;
		$this->payment_at = $pay->getPaymentAt() ? $pay->getPaymentAt()->format($this->dateFormat) : null;
		$this->deadline_at = $pay->getDeadlineAt() ? $pay->getDeadlineAt()->format($this->dateFormat) : null;
	}

	public function generatePay(bool $validate = true): ?PayInterface {
		if ($validate && !$this->validate()) {
			return null;
		}
		return new Pay($this->getValue(), [
			'vat' => $this->getVAT(),
			'transferType' => $this->getTransferType(),
			'paymentAt' => $this->getPaymentAt(),
			'deadlineAt' => $this->getDeadlineAt(),
		]);
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function isPayed(): bool {
		return $this->getPaymentAt() !== null;
	}

	public function getPaymentAt(): ?DateTime {
		if ($this->payment_at) {
			return new DateTime($this->payment_at);
		}
		return null;
	}

	public function getDeadlineAt(): ?DateTime {
		if ($this->deadline_at) {
			return new DateTime($this->deadline_at);
		}
		if (empty($this->payment_at) && $this->deadlineInterval) {
			return (new DateTime())->modify($this->deadlineInterval);
		}
		return null;
	}

	public function getVAT(): ?Decimal {
		if ($this->vat) {
			return new Decimal($this->vat);
		}
		return null;
	}

	public function getTransferType(): int {
		return $this->transferType;
	}

	public static function getTransferTypesNames(): array {
		return IssuePay::getTransferTypesNames();
	}

	public static function getDeadlineIntervals(): array {
		return [
			static::DEADLINE_INTERVAL_3_DAYS => Yii::t('settlement', '3 days'),
			static::DEADLINE_INTERVAL_5_DAYS => Yii::t('settlement', '5 days'),
			static::DEADLINE_INTERVAL_7_DAYS => Yii::t('settlement', '7 days'),
			static::DEADLINE_INTERVAL_14_DAYS => Yii::t('settlement', '14 days'),
			static::DEADLINE_INTERVAL_30_DAYS => Yii::t('settlement', '30 days'),

		];
	}
}
