<?php

namespace common\models\settlement;

use Decimal\Decimal;

interface VATInfo {

	public function getVAT(): ?Decimal;

	public function getValueVAT(): Decimal;

	public function getValueWithVAT(): Decimal;

	public function getValueWithoutVAT(): Decimal;

	public function getVATPercent(): ?string;

	public function hasVAT(): bool;

}
