<?php

namespace common\components;

use common\models\settlement\PayInterface;
use Decimal\Decimal;
use yii\base\Component;

class PayComponent extends Component {

	/**
	 * @param PayInterface[] $pays
	 * @return Decimal
	 */
	public function sum(array $pays): Decimal {
		$sum = new Decimal(0);
		foreach ($pays as $pay) {
			$sum = $sum->add($pay->getValue());
		}
		return $sum;
	}

	public function payedSum(array $pays): Decimal {
		$payed = $this->payedFilter($pays);
		$sum = new Decimal(0);
		foreach ($payed as $pay) {
		 	$sum = $sum->add($pay->getValue());
		}
		return $sum;
	}

	/**
	 * @param PayInterface[] $pays
	 * @return PayInterface[]
	 */
	public function payedFilter(array $pays): array {
		return array_filter($pays, static function (PayInterface $pay): bool {
			return $pay->isPayed();
		});
	}
}
