<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;

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
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::stageAndTypesFixtures(),
				IssueFixtureHelper::customer(true),
				IssueFixtureHelper::issueUsers()
			)
		);
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testEmpty(): void {
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Issue $model */
			$stageType = $model->stageType;
			$minCount = $stageType->min_calculation_count;
			$this->tester->assertGreaterThan(0, $minCount);
			$this->tester->assertLessThanOrEqual($minCount, count($model->payCalculations));
		}
	}

	public function testIssueWithStageWithoutMinCountSettings(): void {
		$this->model->issue_id = 1;
		$this->tester->assertEmpty($this->model->search([])->getTotalCount());
		$this->model->issue_id = 2;
		$this->tester->assertEmpty($this->model->search([])->getTotalCount());
	}

	public function testIssueWithStageWithMinCountSettings(): void {
		$this->model->issue_id = 3;
		$this->tester->assertNotEmpty($this->model->search([])->getTotalCount());
	}

	public function testCustomerSearch(): void {
		$this->model->customerName = 'Lar';
		$models = $this->model->search(['customerLastname' => 'Lar'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Issue $model */
			$this->tester->assertStringStartsWith('Lar', $model->customer->profile->lastname);
		}

		$models = $this->search(['customerLastname' => 'Len'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Issue $model */
			$this->tester->assertStringStartsWith('Len', $model->customer->profile->lastname);
		}
	}

	protected function createModel(): SearchModel {
		return new IssueToCreateCalculationSearch();
	}
}
