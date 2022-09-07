<?php

namespace common\components;

use Decimal\Decimal;
use yii\base\Component;
use yii\base\InvalidArgumentException;

class TaxComponent extends Component {

	public function netto(Decimal $brutto, Decimal $tax): Decimal {
		$this->checkTax($tax);
		if ($tax->isZero()) {
			return $brutto;
		}
		return $brutto->div($this->ratio($tax));
	}

	public function brutto(Decimal $netto, Decimal $tax): Decimal {
		$this->checkTax($tax);
		if ($tax->isZero()) {
			return $netto;
		}
		return $netto->mul($this->ratio($tax));
	}

	private function ratio(Decimal $tax): Decimal {
		return $tax->add(100)->div(100);
	}

	/**
	 * @param Decimal $tax
	 * @throws InvalidArgumentException
	 */
	private function checkTax(Decimal $tax): void {
		if ($tax < 0) {
			throw new InvalidArgumentException('$tax must best greater than 0');
		}
	}
}
