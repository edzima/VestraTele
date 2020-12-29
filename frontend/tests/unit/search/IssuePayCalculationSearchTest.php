<?php

namespace frontend\tests\unit\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use frontend\models\search\IssuePayCalculationSearch;
use frontend\tests\unit\Unit;
use yii\base\InvalidConfigException;

class IssuePayCalculationSearchTest extends Unit {

	use UnitSearchModelTrait;

	protected function _before() {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements()
		));
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testWithoutUsersIds(): void {
		$this->tester->expectThrowable(new InvalidConfigException('Issue users ids must be set.'), function () {
			$this->search([]);
		});
	}

	protected function createModel(): SearchModel {
		return new IssuePayCalculationSearch();
	}
}
