<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\SettlementFixtureHelper;
use common\tests\unit\Unit;
use Decimal\Decimal;

class CalculationTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	/**
	 * @return array
	 */
	public function _fixtures(): array {
		return array_merge(
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
		);
	}

	public function testPayed(): void {
		$model = $this->settlementFixture->grabSettlement('payed-with-single-costs');
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
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
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

}
