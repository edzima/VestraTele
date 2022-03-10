<?php

namespace common\tests\unit\lead\dialer;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\entities\Dialer;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\searches\LeadDialerSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * @property LeadDialerSearch $model
 * @method LeadDialer[] getModels()
 */
class LeadDialerSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::dialer()
		);
	}

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
	}

	public function testOnlyToCall(): void {
		$this->model->onlyToCall = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$first = reset($models);
		$this->tester->assertSame(LeadDialer::PRIORITY_HIGH, $first->priority);
		$this->tester->assertEmpty($first->last_at);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->type->isActive());
			$this->tester->assertContains($model->status, LeadDialer::toCallStatuses());
		}
	}

	public function testLeadStatusNotForDialer(): void {
		$this->model->leadStatusNotForDialer = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue((bool) $model->lead->status->not_for_dialer);
		}
	}

	public function testDialerTypeUser(): void {
		$this->model->typeUserId = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->type->user_id);
		}

		$this->model->typeUserId = 2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->type->user_id);
		}
	}

	public function testLeadStatus(): void {
		$this->model->leadStatusId = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->lead->getStatusId());
		}
	}

	public function testLeadSource(): void {
		$this->model->leadSourceId = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->lead->getSourceId());
		}
	}

	public function testPriority(): void {
		$this->model->priority = LeadDialer::PRIORITY_HIGH;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(LeadDialer::PRIORITY_HIGH, $model->priority);
		}

		$this->model->priority = LeadDialer::PRIORITY_MEDIUM;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(LeadDialer::PRIORITY_MEDIUM, $model->priority);
		}
	}

	public function testStatus(): void {
		$this->model->status = Dialer::STATUS_NEW;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(Dialer::STATUS_NEW, $model->status);
		}

		$this->model->status = Dialer::STATUS_ESTABLISHED;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(Dialer::STATUS_ESTABLISHED, $model->status);
		}
	}

	public function testDialerEmpty(): void {
		$this->model->dialerDestination = LeadDialerSearch::DESTINATION_EMPTY;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue(
				empty($model->destination)
				|| empty($model->lead->getSource()->getDialerPhone())
			);
		}
	}

	protected function createModel(): LeadDialerSearch {
		return new LeadDialerSearch();
	}

}
