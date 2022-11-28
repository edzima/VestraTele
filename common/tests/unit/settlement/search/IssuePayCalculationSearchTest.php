<?php

namespace common\tests\unit\settlement\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
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

	public function testArchived(): void {
		$this->tester->assertFalse($this->model->withArchive);
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->issue->isArchived());
		}
		$this->model->withArchive = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$archived = array_filter($models, function (IssuePayCalculation $model) {
			return $model->getIssueModel()->isArchived();
		});
		$this->tester->assertNotEmpty($archived);
	}

	public function testType(): void {
		$this->model->type = IssueSettlement::TYPE_HONORARIUM;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(IssueSettlement::TYPE_HONORARIUM, $model->getType());
		}

		$this->model->type = IssueSettlement::TYPE_ADMINISTRATIVE;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(IssueSettlement::TYPE_ADMINISTRATIVE, $model->getType());
		}
	}

	public function testIssue(): void {
		$this->model->issue_id = 1;

		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->getIssueId());
		}

		$this->model->issue_id = 3;

		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(3, $model->getIssueId());
		}
	}

	public function testWithoutProvisions(): void {
		$this->model->withoutProvisions = true;
		$models = $this->getModels();
		foreach ($models as $model) {
			foreach ($model->pays as $pay) {
				$this->tester->assertEmpty($pay->provisions);
			}
		}
	}

	public function testProblemStatus(): void {
		$this->model->problem_status = IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND, $model->problem_status);
		}
	}

	public function testOnlyWithProblems(): void {
		$this->model->onlyWithPayProblems = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotNull($model->problem_status);
		}
	}

	public function testOnlyWithoutProblems(): void {
		$this->model->onlyWithPayProblems = false;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNull($model->problem_status);
		}
	}

	public function testValue(): void {
		$this->model->value = '1230';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->getValue()->equals('1230'));
		}

		$this->model->value = '2460';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->getValue()->equals('2460'));
		}
	}

	public function testStageOnCreate(): void {
		$this->model->stage_id = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->stage_id);
		}

		$this->model->stage_id = 2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->stage_id);
		}
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
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertStringContainsString('Lar', $model->getIssueModel()->customer->getFullName());
		}
	}

	public function testOwner(): void {
		$this->model->owner_id = SettlementFixtureHelper::OWNER_JOHN;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(SettlementFixtureHelper::OWNER_JOHN, $model->owner_id);
		}

		$this->model->owner_id = SettlementFixtureHelper::OWNER_NICOLE;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(SettlementFixtureHelper::OWNER_NICOLE, $model->owner_id);
		}
	}

	public function testNotExistedIssueUser(): void {
		$this->model->issueUsersIds = [12312312312];
		$this->tester->assertEmpty($this->getModels());
	}

	public function testIssueUsers(): void {
		$searchIds = [
			UserFixtureHelper::AGENT_PETER_NOWAK,
		];
		$this->model->issueUsersIds = $searchIds;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			foreach ($model->getIssueModel()->users as $issueUser) {
				$this->tester->assertContains($issueUser->user_id, $searchIds);
			}
		}
		$searchIds = [
			UserFixtureHelper::AGENT_PETER_NOWAK,
			UserFixtureHelper::AGENT_AGNES_MILLER,
		];
		$this->model->issueUsersIds = $searchIds;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			foreach ($model->getIssueModel()->users as $issueUser) {
				$this->tester->assertContains($issueUser->user_id, $searchIds);
			}
		}
	}

	protected function createModel(): SearchModel {
		return new IssuePayCalculationSearch();
	}

	/**
	 * @return IssuePayCalculation[]
	 */
	private function getModels(array $params = []): array {
		return $this->model->search($params)->getModels();
	}
}
