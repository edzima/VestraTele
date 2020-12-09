<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use yii\helpers\ArrayHelper;

/**
 * Class CalculationToCreateSearchTest
 *
 * @property IssueToCreateCalculationSearch $model
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CalculationToCreateSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures()),
			IssueFixtureHelper::settlements()
		);
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testEmpty(): void {
		$provider = $this->search([]);
		$ids = ArrayHelper::map($provider->getModels(), 'id', 'id');
		$this->assertCount(3, $ids);
		$this->assertArrayHasKey(3, $ids);
		$this->assertArrayHasKey(4, $ids);
		$this->assertArrayHasKey(5, $ids);
	}

	public function testIssueWithStageWithoutMinCountSettings(): void {
		$this->assertTotalCount(0, ['issue_id' => 1]);
		$this->assertTotalCount(0, ['issue_id' => 2]);
	}

	public function testIssueWithStageWithMinCountSettings(): void {
		$this->assertTotalCount(1, ['issue_id' => 3]);
	}

	public function testCustomerSearch(): void {
		$this->assertTotalCount(2, ['customerLastname' => 'Way']);
		$this->assertTotalCount(1, ['customerLastname' => 'Lar']);
	}

	protected function createModel(): SearchModel {
		return new IssueToCreateCalculationSearch();
	}
}
