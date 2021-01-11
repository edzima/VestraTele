<?php

namespace common\tests\unit\issue\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\search\SummonSearch;
use common\models\issue\Summon;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

class SummonSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::summon(),
		));
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testEmpty(): void {
		$this->assertTotalCount(4);
	}

	public function testIssue(): void {
		$this->assertTotalCount(2, ['issue_id' => 1]);
		$this->assertTotalCount(1, ['issue_id' => 2]);
		$this->assertTotalCount(1, ['issue_id' => 3]);
	}

	public function testCustomerLastname(): void {
		$this->assertTotalCount(2, ['customerLastname' => 'Way']);
	}

	public function testType(): void {
		$this->assertTotalCount(3, ['type' => Summon::TYPE_DOCUMENTS]);
		$this->assertTotalCount(1, ['type' => Summon::TYPE_ANTIVINDICATION]);
		$this->assertTotalCount(0, ['type' => Summon::TYPE_INCOMPLETE_DOCUMENTATION]);
	}

	public function testStatus(): void {
		$this->assertTotalCount(1, ['status' => Summon::STATUS_NEW]);
		$this->assertTotalCount(2, ['status' => Summon::STATUS_IN_PROGRESS]);
		$this->assertTotalCount(1, ['status' => Summon::STATUS_REALIZED]);
		$this->assertTotalCount(0, ['status' => Summon::STATUS_UNREALIZED]);
	}

	public function testContractor(): void {
		$this->assertTotalCount(2, ['contractor_id' => 300]);
		$this->assertTotalCount(2, ['contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$this->assertTotalCount(0, ['contractor_id' => 302]);
	}

	public function testOwner(): void {
		$this->assertTotalCount(2, ['owner_id' => 300]);
		$this->assertTotalCount(2, ['owner_id' => 301]);
		$this->assertTotalCount(0, ['owner_id' => 302]);
	}

	protected function createModel(): SummonSearch {
		return new SummonSearch();
	}
}
