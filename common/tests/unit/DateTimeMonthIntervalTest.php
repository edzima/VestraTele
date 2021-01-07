<?php

namespace common\tests\unit;

use common\helpers\DateTimeHelper;
use DateTime;

class DateTimeMonthIntervalTest extends Unit {

	public const FORMAT = 'Y-m-d';

	public function testFebrurary(): void {
		$this->tester->assertSame(
			'2019-02-28',
			DateTimeHelper::addMonth(new DateTime('2019-01-31'))
				->format(static::FORMAT)
		);
		$this->tester->assertSame(
			'2020-02-29',
			DateTimeHelper::addMonth(new DateTime('2020-01-31'))
				->format(static::FORMAT)
		);
		$this->tester->assertSame(
			'2020-03-31',
			DateTimeHelper::lastDayOfMonth(
				DateTimeHelper::addMonth(new DateTime('2020-02-29'))
			)
				->format(static::FORMAT)
		);
	}
}
