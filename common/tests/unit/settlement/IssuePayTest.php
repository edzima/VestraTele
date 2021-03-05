<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\tests\unit\Unit;

class IssuePayTest extends Unit {

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(true)
			)
		);
	}

	public function testSettlementIndexPart(): void {
		$settlementWithoutPay = $this->grabSettlement('with-problem-status');
		$pays = $settlementWithoutPay->pays;
		$this->tester->assertCount(0, $pays);

		$settlementWithSinglePay = $this->grabSettlement('not-payed');
		$pays = $settlementWithSinglePay->pays;
		$this->tester->assertCount(1, $pays);
		$pay = reset($pays);
		$this->tester->assertSame(1, $pay->getSettlementPartIndex());

		$settlementWithPays = $this->grabSettlement('many-pays');
		$pays = $settlementWithPays->pays;
		$this->tester->assertCount(3, $pays);
		$index = 0;
		foreach ($pays as $pay) {
			$index++;
			$this->tester->assertSame($index, $pay->getSettlementPartIndex());
		}
	}

	private function grabSettlement(string $index): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}
}
