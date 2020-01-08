<?php

namespace common\components;

use yii\base\Component;
use yii\base\InvalidArgumentException;

class TaxComponent extends Component {

	public $default = 23;

	public function netto(float $brutto, float $tax = null): float {
		if ($tax === null) {
			$tax = $this->default;
		}
		$this->checkTax($tax);
		return $brutto / $this->ratio($tax);
	}

	public function brutto(float $netto, float $tax = null): float {
		if ($tax === null) {
			$tax = $this->default;
		}
		$this->checkTax($tax);
		return $netto * $this->ratio($tax);
	}

	private function ratio(float $tax): float {
		return (100 + $tax) / 100;
	}

	/**
	 * @param float $tax
	 * @throws InvalidArgumentException
	 */
	private function checkTax(float $tax): void {
		if ($tax < 0) {
			throw new InvalidArgumentException('$tax must best greater than 0');
		}
	}
}
