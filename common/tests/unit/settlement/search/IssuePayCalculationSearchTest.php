<?php

namespace common\tests\unit\settlement\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueStage;
use common\models\SearchModel;
use common\models\settlement\search\IssuePayCalculationSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * Class IssuePayCalculationSearchTest
 *
 * @property-read IssuePayCalculationSearch $model
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssuePayCalculationSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures($this->fixtures());
		$this->model = $this->createModel();
	}

	/** @todo add Provisions */
	public function fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::agent(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::issueUsers(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::owner(),
			ProvisionFixtureHelper::provision(),
		);
	}

	public function testEmpty(): void {
		$this->assertTotalCount(5);
		$this->model->withArchive = true;
		$this->assertTotalCount(6);
	}

	public function testType(): void {
		$this->assertTotalCount(2, ['type' => IssuePayCalculation::TYPE_ADMINISTRATIVE]);
		$this->assertTotalCount(3, ['type' => IssuePayCalculation::TYPE_HONORARIUM]);
	}

	public function testIssue(): void {
		$this->assertTotalCount(3, ['issue_id' => 1]);
	}

	public function testWithoutProvisions(): void {
		$this->model->withoutProvisions = true;
		$this->assertTotalCount(3);
		$this->assertTotalCount(3);
	}

	public function testProblemStatus(): void {
		$this->model->problem_status = IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND;
		$this->assertTotalCount(1);
	}

	public function testOnlyWithProblems(): void {
		$this->model->onlyWithProblems = true;
		$this->assertTotalCount(2);
	}

	public function testOnlyWithoutProblems(): void {
		$this->model->onlyWithProblems = false;
		$this->assertTotalCount(3);
	}

	public function testValue(): void {
		$this->model->value = '1230';
		$this->assertTotalCount(4);
		$this->model->value = '2460';
		$this->assertTotalCount(1);
	}

	public function testStageOnCreate(): void {
		$this->assertTotalCount(3, ['stage_id' => 1]);
		$this->assertTotalCount(2, ['stage_id' => 2]);
	}

	public function testGetStagesNames(): void {
		$names = IssuePayCalculationSearch::getStagesNames();
		$countAllStages = count(IssueStage::getStagesNames());
		$this->assertNotSame(count($names), $countAllStages);
		$this->assertCount(2, $names);
		$this->assertArrayHasKey(1, $names);
		$this->assertArrayHasKey(2, $names);
	}

	public function testCustomer(): void {
		$this->model->customerLastname = 'Lar';
		$this->assertTotalCount(2);
	}

	public function testOwner(): void {
		$this->model->owner_id = SettlementFixtureHelper::OWNER_JOHN;
		$this->assertTotalCount(3);
		$this->model->owner_id = SettlementFixtureHelper::OWNER_NICOLE;
		$this->assertTotalCount(2);
		$this->model->owner_id = 100000000;
		$this->assertTotalCount(0);
	}

	public function testNotExistedIssueUser(): void {
		$this->model->issueUsersIds = [12312312312];
		$this->assertTotalCount(0);
	}

	public function testAgents(): void {
		$this->model->issueUsersIds = [300, 301];
		$this->assertTotalCount(5);
		$this->model->issueUsersIds = [300];
		$this->assertTotalCount(4);
		$this->model->issueUsersIds = [302];
		$this->assertTotalCount(0);
	}

	public function testLawyers(): void {
		$this->model->issueUsersIds = [200, 201];
		$this->assertTotalCount(5);
		$this->model->issueUsersIds = [200];
		$this->assertTotalCount(3);
		$this->model->issueUsersIds = [201];
		$this->assertTotalCount(2);
		$this->model->issueUsersIds = [203];
		$this->assertTotalCount(0);
	}

	protected function createModel(): SearchModel {
		return new IssuePayCalculationSearch();
	}
}
