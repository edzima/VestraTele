<?php

namespace common\models\settlement;

use DateTime;
use Decimal\Decimal;
use yii\base\BaseObject;

class Pay extends BaseObject implements
	PayInterface {

	private int $transferType;
	private Decimal $value;
	private ?Decimal $vat;
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

	protected function setTransferType(int $type): void {
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

	public function getDeadlineAt(): ?DateTime {
		return $this->deadlineAt;
	}

	public function getVAT(): ?Decimal {
		return $this->vat;
	}

	public function getTransferType(): int {
		return $this->transferType;
	}

}
