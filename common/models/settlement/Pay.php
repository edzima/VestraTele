<?php

namespace common\models\settlement;

use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\BaseObject;

class Pay extends BaseObject implements
	PayInterface {

	private string $transferType;
	private Decimal $value;
	private ?Decimal $vat;
	private ?int $status = null;
	private ?DateTime $paymentAt = null;
	private ?DateTime $deadlineAt = null;

	public function __construct(Decimal $value, $config = []) {
		$this->value = $value;
		parent::__construct($config);
	}

	public function getValue(): Decimal {
		return $this->value;
	}

	protected function setVat(?Decimal $vat): void {
		$this->vat = $vat;
	}

	protected function setTransferType(string $type): void {
		$this->transferType = $type;
	}

	public function isPayed(): bool {
		return $this->getPaymentAt() !== null;
	}

	public function isDelayed(string $range = 'now'): bool {
		if ($this->isPayed() || $this->getDeadlineAt() === null) {
			return false;
		}
		return new DateTime($range) > $this->getDeadlineAt();
	}

	public function getPaymentAt(): ?DateTime {
		return $this->paymentAt;
	}

	public function setPaymentAt(?DateTime $dateTime): void {
		$this->paymentAt = $dateTime;
	}

	protected function setDeadlineAt(?DateTime $datetime): void {
		$this->deadlineAt = $datetime;
	}

	protected function setStatus(?int $status): void {
		$this->status = $status;
	}

	public function getDeadlineAt(): ?DateTime {
		return $this->deadlineAt;
	}

	public function getVAT(): ?Decimal {
		return $this->vat;
	}

	public function getStatus(): ?int {
		return $this->status;
	}

	public function getTransferType(): string {
		return $this->transferType;
	}

	public static function getTransfersTypesNames(): array {
		return [
			static::TRANSFER_TYPE_BANK => Yii::t('settlement', 'Bank Transfer'),
			static::TRANSFER_TYPE_CASH => Yii::t('settlement', 'Cash'),
		];
	}
}
