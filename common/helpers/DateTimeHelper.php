<?php

namespace common\helpers;

use DateTime;

class DateTimeHelper {

	/**
	 * Adding month in PHP using DateTime
	 * class will render the date in a way that
	 * we do not desire, for example adding one month
	 * to 2018-01-31 will result in 2018-03-03, but
	 * we want it to be 2018-02-28 instead.
	 *
	 * This method ensure that adding months
	 * to a date get calculated properly.
	 *
	 *
	 * @param DateTime $startDate
	 * @param int $numberOfMonthsToAdd
	 *
	 * @return DateTime
	 */
	public static function getSameDayNextMonth(DateTime $startDate, int $numberOfMonthsToAdd = 1): DateTime {
		$startDateDay = (int) $startDate->format('j');
		$startDateMonth = (int) $startDate->format('n');
		$startDateYear = (int) $startDate->format('Y');

		$numberOfYearsToAdd = floor(($startDateMonth + $numberOfMonthsToAdd) / 12);
		if ((($startDateMonth + $numberOfMonthsToAdd) % 12) === 0) {
			$numberOfYearsToAdd--;
		}
		$year = $startDateYear + $numberOfYearsToAdd;

		$month = ($startDateMonth + $numberOfMonthsToAdd) % 12;
		if ($month === 0) {
			$month = 12;
		}
		$month = sprintf('%02s', $month);

		$numberOfDaysInMonth = (new DateTime("$year-$month-01"))->format('t');
		$day = $startDateDay;
		if ($startDateDay > $numberOfDaysInMonth) {
			$day = $numberOfDaysInMonth;
		}
		$day = sprintf('%02s', $day);

		return new DateTime("$year-$month-$day");
	}

}
