<?php

namespace common\tests\unit\provision\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\ProvisionReportSearch;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * Class ProvisionReportSearchTest
 *
 * @property ProvisionReportSearch $model
 */
class ProvisionReportSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::agent(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			ProvisionFixtureHelper::provision(),
			ProvisionFixtureHelper::type(),
		));
	}

	public function testAgentWithoutProvisions(): void {
		$this->giveModel(UserFixtureHelper::AGENT_EMILY_PAT, '2020-01-01', '2020-02-01');
		$this->assertTotalCount(0);
	}

	public function testAgentWithProvisions(): void {
		$this->giveModel(UserFixtureHelper::AGENT_PETER_NOWAK, '2020-01-01', '2020-02-01');
		$this->assertTotalCount(0);
	}

	private function giveModel(int $userId, string $dateFrom, string $dateTo): void {
		$this->model = $this->createModel();
		$this->model->to_user_id = $userId;
		$this->model->dateFrom = $dateFrom;
		$this->model->dateTo = $dateTo;
	}

	protected function createModel(): SearchModel {
		return new ProvisionReportSearch();
	}
}
