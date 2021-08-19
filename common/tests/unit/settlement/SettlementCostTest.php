<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\SettlementFixtureHelper;
use common\tests\unit\Unit;
use Decimal\Decimal;

class SettlementCostTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::cost(true)
		);
	}

	public function testSumCostsForSettlementWithoutCosts(): void {
		$settlement = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$this->tester->assertEmpty($settlement->costs);
		$this->tester->assertTrue((new Decimal(0))->equals($settlement->getCostsSum(true)));
		$this->tester->assertTrue((new Decimal(0))->equals($settlement->getCostsSum(false)));
	}

	public function testSumCostForSettlementWithCosts(): void {
		$settlement = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$this->tester->assertNotEmpty($settlement->costs);
		$withVAT = new Decimal(715);
		$this->tester->assertTrue($withVAT->equals($settlement->getCostsSum(true)));
		$withoutVAT = new Decimal(600);
		$this->tester->assertTrue($withoutVAT->equals($settlement->getCostsSum(false)));
	}

	public function testAddSingleCost(): void {
		$settlement = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$this->tester->assertEmpty($settlement->costs);
		$cost1 = $this->settlementFixture->grabCost(0);
		$settlement->linkCosts([$cost1->id]);
		$withVAT = new Decimal(615);
		$this->tester->assertTrue($withVAT->equals($settlement->getCostsSum(true)));
		$withoutVAT = new Decimal(500);
		$this->tester->assertTrue($withoutVAT->equals($settlement->getCostsSum(false)));
	}

	public function testAddDoubleCosts(): void {
		$settlement = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$this->tester->assertEmpty($settlement->costs);
		$cost1 = $this->settlementFixture->grabCost(0);
		$cost2 = $this->settlementFixture->grabCost(1);
		$settlement->linkCosts([$cost1->id, $cost2->id]);
		$withVAT = new Decimal(715);
		$this->tester->assertTrue($withVAT->equals($settlement->getCostsSum(true)));
		$withoutVAT = new Decimal(600);
		$this->tester->assertTrue($withoutVAT->equals($settlement->getCostsSum(false)));
	}

	public function testAddEmptyCosts(): void {
		$settlement = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$this->tester->assertEmpty($settlement->costs);
		$settlement->linkCosts([]);
		$this->tester->assertEmpty($settlement->costs);

		$settlement = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$this->tester->assertNotEmpty($settlement->costs);
		$settlement->linkCosts([]);
		$this->tester->assertNotEmpty($settlement->costs);
	}

}
