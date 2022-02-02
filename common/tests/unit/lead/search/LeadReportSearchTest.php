<?php

namespace common\tests\unit\lead\search;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\searches\LeadReportSearch;
use common\tests\unit\Unit;

class LeadReportSearchTest extends Unit {

	private LeadReportSearch $model;
	/** @var LeadReport[] */
	private ?array $models = null;

	public function _before() {
		parent::_before();
		$this->model = new LeadReportSearch();
	}

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
		);
	}

	public function testType(): void {
		$this->model->lead_type_id = 1;
		$models = $this->getSearchModels();

		foreach ($models as $model) {
			$lead = $model->lead;
			$this->assertSame(1, $lead->getSource()->getType()->getID());
		}
	}

	public function testStatus(): void {
		$this->model->status_id = LeadStatusInterface::STATUS_NEW;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_NEW, $model->status_id);
		}

		$this->model->status_id = LeadStatusInterface::STATUS_ARCHIVE;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_ARCHIVE, $model->status_id);
		}
		$this->model->status_id = 0;
		$this->assertSame(0, $this->model->search()->getTotalCount());
	}

	public function testOldStatus(): void {
		$this->model->old_status_id = LeadStatusInterface::STATUS_NEW;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_NEW, $model->old_status_id);
		}

		$this->model->old_status_id = LeadStatusInterface::STATUS_ARCHIVE;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_ARCHIVE, $model->old_status_id);
		}
		$this->model->old_status_id = 0;
		$this->assertSame(0, $this->model->search()->getTotalCount());
	}

	public function testDetails(): void {
		$this->model->details = 'Some';
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertStringContainsStringIgnoringCase('Some', $model->details);
		}
		$this->model->details = 'BlaBla';
		$models = $this->getSearchModels(true);
		$this->tester->assertEmpty($models);
	}

	public function testOwner(): void {
		$this->model->owner_id = 1;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->owner_id);
		}
		$this->model->owner_id = 2;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->owner_id);
		}
		$this->model->owner_id = 3;
		$models = $this->getSearchModels(true);
		$this->tester->assertEmpty($models);
	}

	public function testLeadUserId(): void {
		$this->model->lead_user_id = 1;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertTrue($model->lead->isForUser(1));
		}
		$this->model->owner_id = 2;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertTrue(
				$model->lead->isForUser(1)
				|| $model->owner_id === 2
			);
		}
	}

	public function testDate(): void {
		$this->model->from_at = '2020-01-01';
		$models = $this->getSearchModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertGreaterThanOrEqual(strtotime($this->model->from_at), strtotime($model->created_at));
		}
		$this->model->to_at = '2020-01-01';
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->tester->assertGreaterThanOrEqual(strtotime($this->model->from_at), strtotime($model->created_at));
			$this->tester->assertLessThanOrEqual(strtotime($this->model->to_at), strtotime($model->created_at));
		}
		$this->model->from_at = '2020-02-01';
		$models = $this->getSearchModels(true);
		$this->tester->assertEmpty($models);
	}

	/**
	 * @param bool $refresh
	 * @return LeadReport[]
	 */
	private function getSearchModels(bool $refresh = false): array {
		if ($refresh || $this->models === null) {
			$this->models = $this->model->search()->getModels();
		}
		return $this->models;
	}

}
