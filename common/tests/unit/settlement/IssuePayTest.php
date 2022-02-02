<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\tests\unit\Unit;

class IssuePayTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay()
		);
	}

	public function testSettlementIndexPart(): void {
		$settlementWithoutPay = $this->settlementFixture->grabSettlement('with-problem-status');
		$pays = $settlementWithoutPay->pays;
		$this->tester->assertCount(0, $pays);

		$settlementWithSinglePay = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$pays = $settlementWithSinglePay->pays;
		$this->tester->assertCount(1, $pays);
		$pay = reset($pays);
		$this->tester->assertSame(1, $pay->getSettlementPartIndex());

		$settlementWithPays = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$pays = $settlementWithPays->pays;
		$this->tester->assertCount(3, $pays);
		$index = 0;
		$count = count($pays);
		foreach ($pays as $pay) {
			$index++;
			$this->tester->assertSame($index, $pay->getSettlementPartIndex());
			$this->tester->assertSame("$index/$count", $pay->getPartInfo());
		}
	}

}
