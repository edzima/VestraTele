<?php

namespace backend\tests\unit\issue\search;

use backend\modules\issue\models\search\IssueSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\tests\_support\UnitSearchModelTrait;

class IssueSearchTest extends Unit {

	use UnitSearchModelTrait;

	public static bool $hasFixture = false;

	public function _before(): void {
		$this->getModule('Yii2')->_reconfigure(['cleanup' => false]);
		if (!static::$hasFixture) {
			$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
			static::$hasFixture = true;
		}
		$this->model = $this->createModel();
		parent::_before();
	}

	protected function createModel(): IssueSearch {
		return new IssueSearch();
	}

	public function testWithoutSearchParams(): void {
		$this->assertTotalCount(IssueFixtureHelper::ISSUE_COUNT - IssueFixtureHelper::ARCHIVED_ISSUE_COUNT);
	}

	public function testWithArchiveAsParams(): void {
		$this->assertTotalCount(IssueFixtureHelper::ISSUE_COUNT - IssueFixtureHelper::ARCHIVED_ISSUE_COUNT, ['withArchive' => true]);
	}

	public function testWithArchive(): void {
		$this->model->withArchive = true;
		$this->assertTotalCount(IssueFixtureHelper::ISSUE_COUNT);
	}

	public function testForId(): void {
		$this->assertTotalCount(0, ['issue_id' => 200]);
		$this->assertTotalCount(1, ['issue_id' => 1]);
	}

	public function testForType(): void {
		$this->assertTotalCount(2, ['type_id' => 1]);
		$this->assertTotalCount(3, ['type_id' => 2]);
	}

	public function testForStage(): void {
		$this->assertTotalCount(1, ['stage_id' => 1]);
		$this->assertTotalCount(3, ['stage_id' => 2]);
	}

	public function testExcludedStages(): void {
		$this->assertTotalCount(2, ['excludedStages' => [2]]);
		$this->assertTotalCount(4, ['excludedStages' => [1]]);
	}

	public function testCustomerLastname(): void {
		$this->assertTotalCount(2, ['customerLastname' => 'Lars']);
	}

	public function testForAgent(): void {
		$this->assertTotalCount(2, ['agent_id' => 300]);
	}

	public function testForLawyer(): void {
		$this->assertTotalCount(1, ['lawyer_id' => 200]);
	}

	public function testForTele(): void {
		$this->assertTotalCount(1, ['tele_id' => 400]);
	}

	public function testCustomerWithAgent(): void {
		$this->assertTotalCount(1, ['agent_id' => 300, 'customerLastname' => 'Lar']);
	}

	public function testForAgentAndLawyer(): void {
		$this->assertTotalCount(1, ['agent_id' => 300, 'lawyer_id' => 200]);
	}

	public function testOnlyWithPayedPays(): void {
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements()
			)
		);
		$this->assertTotalCount(2, ['onlyWithPayedPay' => true]);
	}
}
