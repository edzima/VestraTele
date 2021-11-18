<?php

namespace common\tests\unit\settlement\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\SearchModel;
use common\models\settlement\search\IssuePaySearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

/**
 * Class PaySearchTest
 *
 * @property IssuePaySearch $model
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PaySearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->model = $this->createModel();
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::agent(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::issueUsers(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
		);
	}

	public function testIssueTypes(): void {
		$this->model->issueTypesIds = [1];
		$models1 = $this->getModels();
		$this->tester->assertNotEmpty($models1);
		foreach ($models1 as $model) {
			$this->tester->assertSame(1, $model->issue->type_id);
		}
		$this->model->issueTypesIds = [2];
		$models2 = $this->getModels();
		$this->tester->assertNotEmpty($models2);
		foreach ($models2 as $model) {
			$this->tester->assertSame(2, $model->issue->type_id);
		}

		$this->model->issueTypesIds = [1, 2];
		$models1or2 = $this->getModels();
		$this->tester->assertNotEmpty($models1or2);
		$this->tester->assertSame(count($models1or2), count($models1) + count($models2));
	}

	public function testAll(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_ALL;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$payed = array_filter($models, function (IssuePay $pay): bool {
			return $pay->isPayed();
		});
		$this->tester->assertNotEmpty($payed);

		$notPayed = array_filter($models, function (IssuePay $pay): bool {
			return !$pay->isPayed();
		});
		$this->tester->assertNotEmpty($notPayed);
		$this->tester->assertSame(count($models), count($payed) + count($notPayed));
	}

	public function testPayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_PAYED;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->isPayed());
		}
	}

	public function testNotPayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;

		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->isPayed());
			$this->tester->assertTrue($model->isDelayed());
		}

		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_NONE;

		$models = $this->getModels();
		$this->tester->assertEmpty($models);
	}

	public function testAllDelayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->isPayed());
			$this->tester->assertTrue($model->isDelayed());
		}
	}

	public function testMaxDelayedRange(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_MAX_3_DAYS;
		$this->tester->assertEmpty($this->getModels());
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 2 days')),
		]);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 3 days')),
		]);
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$this->tester->assertCount(2, $models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->isDelayed());
		}

		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 4 days')),
		]);

		$this->tester->assertCount(2, $this->getModels());
		$this->model->delay = IssuePaySearch::DELAY_MIN_3_MAX_7_DAYS;
		$this->tester->assertCount(2, $this->getModels());
		$this->model->delay = IssuePaySearch::DELAY_MIN_7_MAX_14_DAYS;
		$this->tester->assertEmpty($this->getModels());
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 7 days')),
		]);
		$this->tester->assertCount(1, $this->getModels());
	}

	public function testMinDelayedRange(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_MIN_30_DAYS;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->isDelayed('- 30 days'));
		}
	}

	public function testAgent(): void {
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_ALL;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->issue->agent->id);
		}
		$this->model->agent_id = UserFixtureHelper::AGENT_AGNES_MILLER;

		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_AGNES_MILLER, $model->issue->agent->id);
		}

		$this->model->agent_id = [UserFixtureHelper::AGENT_PETER_NOWAK, UserFixtureHelper::AGENT_AGNES_MILLER];
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$agentId = $model->issue->agent->id;
			$this->tester->assertTrue(
				$agentId === UserFixtureHelper::AGENT_PETER_NOWAK
				|| $agentId === UserFixtureHelper::AGENT_AGNES_MILLER
			);
		}

		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;

		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->issue->agent->id);
		}
	}

	public function testCustomer(): void {
		$this->model->customerLastname = 'Lar';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertStringContainsString('Lar', $model->issue->customer->getFullName());
		}

		$this->model->customerLastname = 'Way';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertStringContainsString('Way', $model->issue->customer->getFullName());
		}
	}

	public function testArchive(): void {
		$this->tester->assertTrue($this->model->withArchive);
		$this->model->withArchive = false;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->issue->isArchived());
		}
		$this->model->withArchive = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$archived = array_filter($models, function (IssuePay $model) {
			return $model->issue->isArchived();
		});
		$this->tester->assertNotEmpty($archived);
	}

	/**
	 * @param array $params
	 * @return IssuePay[]
	 * @throws InvalidConfigException
	 */
	public function getModels(array $params = []): array {
		return $this->model->search($params)->getModels();
	}

	protected function createModel(): SearchModel {
		return new IssuePaySearch();
	}
}
