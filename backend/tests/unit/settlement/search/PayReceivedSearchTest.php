<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\PayReceivedSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\SearchModel;
use common\models\settlement\PayReceived;
use common\tests\_support\UnitSearchModelTrait;

/**
 * Class CalculationToCreateSearchTest
 *
 * @property PayReceivedSearch $model
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PayReceivedSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::agent(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::issueUsers(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::payReceived(),
		));
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testAgent(): void {
		$this->model->issueAgent = UserFixtureHelper::AGENT_PETER_NOWAK;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->getIssueModel()->agent->id);
		}
	}

	public function testUser(): void {
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->user_id);
		}
		$this->model->user_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_AGNES_MILLER, $model->user_id);
		}
		$this->model->user_id = UserFixtureHelper::AGENT_TOMMY_SET;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_TOMMY_SET, $model->user_id);
		}
	}

	public function testCustomerLastname(): void {
		$this->model->customerLastname = 'Lar';
		$this->assertTotalCount(1);
		$this->model->customerLastname = 'Way';
		$this->assertTotalCount(5);
	}

	public function testTransferStatus(): void {
		$this->model->transferStatus = PayReceivedSearch::TRANFER_STATUS_NO;
		$this->assertTotalCount(4);
		$this->model->transferStatus = PayReceivedSearch::TRANFER_STATUS_YES;
		$this->assertTotalCount(2);
	}

	protected function createModel(): SearchModel {
		return new PayReceivedSearch();
	}

	/**
	 * @param array $params
	 * @return PayReceived[]
	 */
	private function getModels(array $params = []): array {
		return $this->model->search($params)->getModels();
	}
}
