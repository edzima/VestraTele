<?php

namespace common\helpers;

use DateTime;

class DateTimeHelper {

	public static function addMonth(DateTime $dateTime): DateTime {
		$dt = clone($dateTime);
		$day = $dt->format('j');
		$dt->modify('first day of +1 month');
		$dt->modify('+' . (min($day, $dt->format('t')) - 1) . ' days');
		return $dt;
	}
}
