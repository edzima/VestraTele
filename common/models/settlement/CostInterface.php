<?php

namespace common\models\settlement;

use Decimal\Decimal;

interface CostInterface {

	public function getValue(): Decimal;

	public function getIsSettled(): bool;

	public function getIsConfirmed(): bool;

	public function getType(): CostType;

	public function getTypeName(): string;

}
