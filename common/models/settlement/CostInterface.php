<?php

namespace common\models\settlement;

use Decimal\Decimal;

interface CostInterface {

	public function getValue(): Decimal;

	public function getIsSettled(): bool;

	public function getTypeName(): string;

}
