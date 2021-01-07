<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\tests\unit\Unit;
use Decimal\Decimal;

class CalculationTest extends Unit {

	/**
	 * @return array
	 */
	public function _fixtures(): array {
		return array_merge(IssueFixtureHelper::fixtures(), IssueFixtureHelper::settlements());
	}

	public function testPayed(): void {
		$model = $this->grabCalculation('payed');
		$this->tester->assertTrue($model->isPayed());
		$this->tester->assertTrue(
			(new Decimal(1230))
				->equals($model->getPays()->getPayedSum())
		);
		$this->tester->assertTrue(
			(new Decimal(0))
				->equals($model->getNotPayedPays()->getValueSum())
		);
	}

	public function testNotPayed(): void {
		$model = $this->grabCalculation('not-payed');
		$this->tester->assertFalse($model->isPayed());
		$this->tester->assertTrue(
			(new Decimal(0))
				->equals($model->getPays()->getPayedSum())
		);
		$this->tester->assertTrue(
			(new Decimal(1230))
				->equals($model->getNotPayedPays()->getValueSum())
		);
	}

	protected function grabCalculation($index): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}
}
