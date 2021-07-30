<?php

namespace common\models\settlement;

use DateTime;

interface PayedInterface {

	public function isPayed(): bool;

	public function getPaymentAt(): ?DateTime;

	public function getDeadlineAt(): ?DateTime;

	public function isDelayed(string $range = 'now'): bool;
}
