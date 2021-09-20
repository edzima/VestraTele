<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\search\IssueLeadsSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueUserQuery;
use common\models\SearchModel;
use common\modules\lead\models\Lead;
use common\tests\_support\UnitSearchModelTrait;

class IssueFromLeadTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(true),
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status()
		);
	}

	public function testCustomerQuery(): void {
		/** @var IssueUserQuery $query */
		$query = $this->search([])->query;
		codecept_debug($query->createCommand()->getRawSql());
		codecept_debug($query->asArray()->all());
	}

	public function testWithoutStatus(): void {
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
	}

	protected function createModel(): SearchModel {
		return new IssueLeadsSearch();
	}
}
