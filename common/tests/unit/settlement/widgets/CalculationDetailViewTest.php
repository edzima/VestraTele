<?php

namespace common\tests\unit\settlement\widgets;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\tests\unit\Unit;
use common\widgets\settlement\SettlementDetailView;
use yii\base\InvalidConfigException;

class CalculationDetailViewTest extends Unit {

	public function _before() {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements()
		));
		parent::_before();
	}

	public function testWithoutModel(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			SettlementDetailView::widget();
		});
	}

	public function testNotPayed(): void {
		$content = SettlementDetailView::widget(['model' => $this->grabCollection('not-payed')]);
	}

	protected function grabCollection($index): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}
}
