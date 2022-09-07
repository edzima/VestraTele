<?php

namespace common\tests\unit\issue\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\search\SummonSearch;
use common\models\issue\Summon;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * @property SummonSearch $model
 * @method Summon[] getModels(array $params = [])
 */
class SummonSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->model = $this->createModel();
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::issueUsers(),
			IssueFixtureHelper::summon()
		);
	}

	public function testIssue(): void {
		$this->model->issue_id = 1;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->issue_id, 1);
		}
		$this->model->issue_id = 2;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->issue_id, 2);
		}
	}

	public function testCustomerLastname(): void {
		$this->model->customerLastname = 'Lar';
		foreach ($this->getModels() as $model) {
			$this->tester->assertStringContainsString('Lar', $model->getIssueModel()->customer->getFullName());
		}

		$this->model->customerLastname = 'Way';
		foreach ($this->getModels() as $model) {
			$this->tester->assertStringContainsString('Way', $model->getIssueModel()->customer->getFullName());
		}
	}

	public function testType(): void {
		$this->model->type_id = 1;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->type_id, 1);
		}
		$this->model->type_id = 2;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->type_id, 2);
		}
	}

	public function testStatus(): void {
		$this->model->status = Summon::STATUS_NEW;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->status, Summon::STATUS_NEW);
		}
		$this->model->status = Summon::STATUS_IN_PROGRESS;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->status, Summon::STATUS_IN_PROGRESS);
		}
	}

	public function testContractor(): void {
		$this->model->contractor_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->contractor_id, UserFixtureHelper::AGENT_PETER_NOWAK);
		}
		$this->model->contractor_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->contractor_id, UserFixtureHelper::AGENT_AGNES_MILLER);
		}
	}

	public function testOwner(): void {
		$this->model->owner_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->owner_id, UserFixtureHelper::AGENT_PETER_NOWAK);
		}
		$this->model->owner_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		foreach ($this->getModels() as $model) {
			$this->tester->assertSame($model->owner_id, UserFixtureHelper::AGENT_AGNES_MILLER);
		}
	}

	protected function createModel(): SummonSearch {
		return new SummonSearch();
	}
}
