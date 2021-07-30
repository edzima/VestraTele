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
		$this->assertTotalCount(0, ['issue_id' => 1]);
		$this->assertTotalCount(0, ['issue_id' => 2]);
	}

	public function testIssueWithStageWithMinCountSettings(): void {
		$this->assertTotalCount(1, ['issue_id' => 3]);
	}

	public function testCustomerSearch(): void {
		$models = $this->search(['customerLastname' => 'Way'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Issue $model */
			$this->tester->assertStringStartsWith('Way', $model->customer->profile->lastname);
		}

		$models = $this->search(['customerLastname' => 'Lar'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Issue $model */
			$this->tester->assertStringStartsWith('Lar', $model->customer->profile->lastname);
		}
	}

	protected function createModel(): SearchModel {
		return new IssueToCreateCalculationSearch();
	}
}
