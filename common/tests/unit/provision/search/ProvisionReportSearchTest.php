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
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::pay(codecept_data_dir() . 'provision/'),
			SettlementFixtureHelper::settlement(codecept_data_dir() . 'provision/'),
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::provision(),
		);
	}

	public function testWithoutUser(): void {
		$this->tester->assertFalse($this->model->validate());
		$this->tester->assertSame('To User cannot be blank.', $this->model->getFirstError('to_user_id'));
		$this->tester->assertSame(0, $this->model->search([])->getTotalCount());
	}

	public function testBindToUserIdWithSearchParam(): void {
		$this->model->to_user_id = 1;
		$this->model->search(['to_user_id' => 2]);
		$this->tester->assertSame(1, $this->model->to_user_id);
	}

	public function testHasHiddenProvision(): void {
		$this->model->to_user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->tester->assertTrue($this->model->hasHiddenProvisions());
	}

	protected function createModel(): SearchModel {
		return new ProvisionReportSearch([
			'defaultCurrentMonth' => false,
		]);
	}
}
