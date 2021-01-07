<?php

namespace common\models\settlement;

use DateTime;
use Decimal\Decimal;

interface PayInterface {

	public function getValue(): Decimal;

	public function getVAT(): ?Decimal;

	public function getTransferType(): int;

	public function isPayed(): bool;

	public function getPaymentAt(): ?DateTime;

	public function getDeadlineAt(): ?DateTime;

	public function isDelayed(string $range = 'now'): bool;
}
