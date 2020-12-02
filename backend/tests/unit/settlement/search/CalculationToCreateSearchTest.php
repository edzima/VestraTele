<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;

class CalculationToCreateSearchTest extends Unit {

	public function _before() {
		$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
		parent::_before();
	}

	public function testWithoutParams(): void {
		$model = new IssueToCreateCalculationSearch();
		$model->search([]);
	}
}
