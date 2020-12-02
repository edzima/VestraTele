<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use yii\base\InvalidConfigException;

class CalculationProblemStatusFormTest extends Unit {

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(array_merge(IssueFixtureHelper::fixtures(), IssueFixtureHelper::settlements()));
	}

	public function testSetStatusForNotPayed(): void {
		/** @var IssuePayCalculation $calculation */
		$calculation = $this->tester->grabFixture('calculation', 'not-payed');
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
			new CalculationProblemStatusForm($this->tester->grabFixture('calculation', 'payed'));
		});
	}

}
