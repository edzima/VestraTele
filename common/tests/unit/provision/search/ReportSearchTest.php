<?php

namespace common\tests\unit\provision\search;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\search\ReportSearch;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

class ReportSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return array_merge(
			['agent' => UserFixtureHelper::agent()],
			ProvisionFixtureHelper::provision(),
			ProvisionFixtureHelper::type(),
		);
	}

	public function testEmpty(): void {
		$this->assertTotalCount(3);
	}

	protected function createModel(): SearchModel {
		return new ReportSearch();
	}
}
