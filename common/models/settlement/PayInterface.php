<?php

namespace common\models\settlement;

use Decimal\Decimal;

interface PayInterface extends TransferType, PayedInterface {

	public function getValue(): Decimal;

	public function getVAT(): ?Decimal;

	public function getStatus(): ?int;

}
