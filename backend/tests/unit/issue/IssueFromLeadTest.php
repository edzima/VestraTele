<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueLeadsSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;

class IssueFromLeadTest extends Unit {

	use UnitSearchModelTrait;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(true),
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status()
		);
	}

	public function testWithoutStatus(): void {
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
	}

	protected function createModel(): SearchModel {
		return new IssueLeadsSearch();
	}
}
