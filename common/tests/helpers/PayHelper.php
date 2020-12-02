<?php

namespace common\tests\helpers;

use common\models\settlement\Pay;
use common\models\settlement\PayInterface;
use Decimal\Decimal;

class PayHelper {

	/**
	 * @param Decimal|string|int|float $value
	 * @param array $config
	 * @return PayInterface
	 */
	public static function create($value, array $config = []): PayInterface {
		if (!$value instanceof Decimal) {
			$value = new Decimal($value);
		}
		return new Pay($value, $config);
	}
}
