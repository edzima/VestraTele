<?php

namespace frontend\tests\unit\search;

use common\fixtures\helpers\IssueFixtureHelper;
use frontend\models\search\IssueSearch;
use frontend\tests\unit\Unit;
use yii\base\InvalidConfigException;

class IssueSearchTest extends Unit {

	protected function _before() {
		$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
		parent::_before();
	}

	public function testSearchWithotUserId(): void {
		$model = new IssueSearch();
		$this->tester->expectThrowable(InvalidConfigException::class, function () use ($model) {
			$model->search([]);
		});
	}
}
