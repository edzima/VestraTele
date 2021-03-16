<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssueCostSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\tests\_support\UnitSearchModelTrait;

/**
 * Class IssueCostSearchTest
 *
 * @property IssueCostSearch $model
 */
class IssueCostSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::stageAndTypesFixtures(),
				SettlementFixtureHelper::settlement(),
				SettlementFixtureHelper::cost(true)
			)
		);
	}

	public function testEmpty(): void {
		$this->assertTotalCount(5);
	}

	public function testSettled(): void {
		$this->assertTotalCount(1, ['settled' => true]);
	}

	public function testNotSettled(): void {
		$this->assertTotalCount(4, ['settled' => false]);
	}

	public function testWithSettlements(): void {
		$this->assertTotalCount(3, ['withSettlements' => true]);
	}

	public function testWithoutSettlements(): void {
		$this->assertTotalCount(2, ['withSettlements' => false]);
	}

	public function testIssueType(): void {
		$this->assertTotalCount(3, ['issueType' => 1]);
		$this->assertTotalCount(2, ['issueType' => 2]);
		$this->assertTotalCount(0, ['issueType' => 3]);
	}

	public function testIssueStage(): void {
		$this->assertTotalCount(2, ['issueStage' => 1]);
		$this->assertTotalCount(3, ['issueStage' => 2]);
		$this->assertTotalCount(0, ['issueStage' => 3]);
	}

	protected function createModel(): IssueCostSearch {
		return new IssueCostSearch();
	}
}
