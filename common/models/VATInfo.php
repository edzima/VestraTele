<?php

namespace common\models;

use Decimal\Decimal;

interface VATInfo {

	public function getVAT(): Decimal;

	public function getValueWithVAT(): Decimal;

	public function getValueWithoutVAT(): Decimal;

	public function getVATPercent(): string;

}
