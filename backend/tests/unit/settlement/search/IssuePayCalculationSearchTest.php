<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use yii\data\ActiveDataProvider;

class IssuePayCalculationSearchTest extends Unit {

	private IssuePayCalculationSearch $model;

	public function _before(): void {
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
			));
		$this->model = new IssuePayCalculationSearch();
		parent::_before();
	}

	public function testEmpty(): void {
		$provider = $this->search([]);
		$this->assertSame(3, $provider->getTotalCount());
	}

	public function testIssue(): void {
		$provider = $this->search(['issue_id' => 1]);
		$this->assertSame(2, $provider->getTotalCount());
	}

	public function testWithoutProvisions(): void {
		$this->model->withoutProvisions = true;
		$provider = $this->search([]);
		$this->assertSame(3, $provider->getTotalCount());
	}

	protected function search(array $params): ActiveDataProvider {
		$params[$this->model->formName()] = $params;
		return $this->model->search($params);
	}
}
