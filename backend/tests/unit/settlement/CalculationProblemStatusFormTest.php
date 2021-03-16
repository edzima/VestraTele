<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use yii\base\InvalidConfigException;

class CalculationProblemStatusFormTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;

	public function _before() {
		parent::_before();
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay()
		));
	}

	public function testSetStatusForNotPayed(): void {
		$calculation = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$this->tester->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
		]);
		$model = new CalculationProblemStatusForm($calculation);
		$this->tester->assertNotSame(IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND, $calculation->problem_status);
		$model->status = IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND;
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'problem_status' => IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND,
		]);
		$this->tester->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
		]);
	}

	public function testSetStatusForPayed(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			new CalculationProblemStatusForm(
				$this->settlementFixture->grabSettlement('payed-with-single-costs')
			);
		});
	}

}
