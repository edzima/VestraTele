<?php

namespace common\widgets\grid;

use Decimal\Decimal;

class CurrencyColumn extends DataColumn {

	public $noWrap = true;
	public bool $contentBold = true;

	public $attribute = 'value';
	public $format = 'currency';
	public $width = '100px';

	public function init(): void {
		if ($this->pageSummary && empty($this->pageSummaryFunc)) {
			$this->pageSummaryFunc = static function (array $decimals): Decimal {
				$sum = new Decimal(0);
				foreach ($decimals as $decimal) {
					if (is_float($decimal)) {
						$decimal = (string) $decimal;
					}
					$sum = $sum->add($decimal);
				}
				return $sum;
			};
		}
		parent::init();
	}

}
