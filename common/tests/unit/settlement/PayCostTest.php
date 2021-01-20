<?php

namespace common\tests\unit\settlement;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\tests\unit\Unit;
use Decimal\Decimal;

class PayCostTest extends Unit {

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(true)
			)
		);
	}

	public function testCostSum(): void {
		/** @var IssuePayCalculation $model */
		$model = $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$sum = new Decimal(700);
		$this->tester->assertTrue($sum->equals($model->getCostsSum(true)));
	}

	public function testWithoutCosts(): void {
		/** @var IssuePayCalculation $model */
		$model = $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, 'many-pays');
		$this->assertTrue($model->getCostsSum()->equals(new Decimal(0)));
		foreach ($model->pays as $pay) {
			$this->assertTrue($pay->getCosts()->equals(new Decimal(0)));
		}
	}

	public function testPayCost(): void {
		/** @var IssuePayCalculation $model */
		$model = $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, 'many-pays');
		$model->linkCosts([1, 2]);
		$sum = new Decimal(700);
		$this->tester->assertTrue($sum->equals($model->getCostsSum(true)));
		$costPay = new Decimal(350);
		foreach ($model->pays as $pay) {
			$this->assertTrue($pay->getCosts(true)->equals($costPay));
		}
	}

}
