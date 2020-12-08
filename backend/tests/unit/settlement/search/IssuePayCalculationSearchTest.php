<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\issue\models\IssueStage;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;

/**
 * Class IssuePayCalculationSearchTest
 *
 * @property-read IssuePayCalculationSearch $model
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssuePayCalculationSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->model = $this->createModel();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
			));
		parent::_before();
	}

	public function testEmpty(): void {
		$this->assertTotalCount(5);
	}

	public function testType(): void {
		$this->assertTotalCount(2, ['type' => IssuePayCalculation::TYPE_ADMINISTRATIVE]);
		$this->assertTotalCount(3, ['type' => IssuePayCalculation::TYPE_HONORARIUM]);
	}

	public function testIssue(): void {
		$this->assertTotalCount(3, ['issue_id' => 1]);
	}

	public function testWithoutProvisions(): void {
		$this->model->withoutProvisions = true;
		$this->assertTotalCount(4);
	}

	public function testProblemStatus(): void {
		$this->model->problem_status = IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND;
		$this->assertTotalCount(1);
	}

	public function testOnlyWithProblems(): void {
		$this->model->onlyWithProblems = true;
		$this->assertTotalCount(2);
	}

	public function testOnlyWithoutProblems(): void {
		$this->model->onlyWithProblems = false;
		$this->assertTotalCount(3);
	}

	public function testValue(): void {
		$this->model->value = '1230';
		$this->assertTotalCount(4);
		$this->model->value = '2460';
		$this->assertTotalCount(1);
	}

	public function testStageOnCreate(): void {
		$this->assertTotalCount(3, ['stage_id' => 1]);
		$this->assertTotalCount(2, ['stage_id' => 2]);
	}

	public function testGetStagesNames(): void {
		$names = IssuePayCalculationSearch::getStagesNames();
		$countAllStages = (int) IssueStage::find()->count();
		$this->assertNotSame(count($names), $countAllStages);
		$this->assertCount(2,$names);
		$this->assertArrayHasKey(1,$names);
		$this->assertArrayHasKey(2,$names);

	}

	public function testCustomer(): void {
		$this->model->customerLastname = 'Lar';
		$this->assertTotalCount(2);
	}

	protected function createModel(): SearchModel {
		return new IssuePayCalculationSearch();
	}
}
