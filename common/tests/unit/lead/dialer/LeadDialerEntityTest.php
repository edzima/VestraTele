<?php

namespace common\tests\unit\lead\dialer;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\entities\LeadDialerEntity;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\tests\unit\Unit;

class LeadDialerEntityTest extends Unit {

	private const TYPE_ACTIVE = 1;
	private const TYPE_INACTIVE = 2;

	private int $leadStatusForDialer;
	private int $leadStatusNotForDialer;

	private LeadFixtureHelper $leadFixtureHelper;

	private LeadDialerEntity $entity;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::dialer()
		);
	}

	public function _before(): void {
		$this->leadFixtureHelper = new LeadFixtureHelper($this->tester);
		$this->leadStatusNotForDialer = $this->tester->haveRecord(LeadStatus::class, [
			'name' => 'Not For Dialer, Not CALL!',
			'not_for_dialer' => true,
		]);

		$this->leadStatusForDialer = $this->tester->haveRecord(LeadStatus::class, [
			'name' => 'For Dialer, Make CALL!',
			'not_for_dialer' => false,
		]);
		LeadStatus::getModels(true);

		parent::_before();
	}

	public function testInactiveDialerType(): void {
		$leadDialer = $this->haveLeadDialer($this->haveLead(), ['type_id' => static::TYPE_INACTIVE]);

		$this->giveEntity($leadDialer);

		$this->assertSameStatus(LeadDialerEntity::STATUS_DIALER_TYPE_INACTIVE);
	}

	public function testLeadWithoutPhone(): void {
		$leadDialer = $this->haveLeadDialer(
			$this->haveLead(['phone' => ''])
		);

		$this->giveEntity($leadDialer);

		$this->assertSameStatus(LeadDialerEntity::STATUS_EMPTY_LEAD_PHONE);
	}

	public function testLeadSourceWithoutDialerPhone(): void {
		$source = $this->tester->grabRecord(LeadSource::class, [
			'dialer_phone' => null,
		]);
		$leadDialer = $this->haveLeadDialer(
			$this->haveLead(['source_id' => $source->id])
		);

		$this->giveEntity($leadDialer);

		$this->assertSameStatus(LeadDialerEntity::STATUS_EMPTY_LEAD_SOURCE_DIALER_PHONE);
	}

	public function testCurrentLeadStatusNotForDialer(): void {
		$leadDialer = $this->haveLeadDialer(
			$this->haveLead(['status_id' => $this->leadStatusNotForDialer])
		);

		$this->giveEntity($leadDialer);

		$this->assertSameStatus(LeadDialerEntity::STATUS_CURRENT_LEAD_STATUS_NOT_FOR_DIALER);
	}

	public function testSameLeadStatusNotForDialer(): void {
		$leadWithStatusNotForDialer = $this->haveLead(['status_id' => $this->leadStatusNotForDialer]);
		$leadWithStatusForDialer = $this->haveLead(['status_id' => $this->leadStatusForDialer]);

		$this->giveEntity(
			$this->haveLeadDialer($leadWithStatusForDialer)
		);

		$this->assertSameStatus(LeadDialerEntity::STATUS_SAME_LEAD_STATUS_NOT_FOR_DIALER);

		$this->giveEntity(
			$this->haveLeadDialer($leadWithStatusNotForDialer)
		);

		$this->assertSameStatus(LeadDialerEntity::STATUS_CURRENT_LEAD_STATUS_NOT_FOR_DIALER);
	}

	public function testUpdateStatusAsCalling(): void {
		$leadDialer = $this->haveLeadDialer($this->haveLead());

		$this->giveEntity($leadDialer);

		$this->entity->updateStatus(LeadDialerEntity::STATUS_CALLING);

		$this->tester->assertSame(1, count($this->entity->getConnectionAttempts()));

		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $leadDialer->id,
			'status' => LeadDialerEntity::STATUS_CALLING,
		]);

		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => $leadDialer->lead_id,
			'owner_id' => $leadDialer->type->user_id,
			'old_status_id' => $leadDialer->lead->getStatusId(),
			'status_id' => LeadDialerEntity::STATUS_CALLING,
		]);

		$this->leadFixtureHelper
			->dontSeeLead(['id' => $leadDialer->lead_id, 'status_id' => LeadDialerEntity::STATUS_CALLING]
			);

		$this->tester->assertFalse($this->entity->shouldCall());
		$this->assertSameStatus(LeadDialerEntity::STATUS_CALLING);
	}

	public function testStatusForCalling(): void {
		$leadDialer = $this->haveLeadDialer($this->haveLead(), [
			'status' => LeadDialerEntity::STATUS_CALLING,
		]);

		$this->giveEntity($leadDialer);
		$this->assertSameStatus(LeadDialerEntity::STATUS_CALLING);
		$this->tester->assertFalse($this->entity->shouldCall());
	}

	private function assertSameStatus(int $status): void {
		$this->tester->assertSame($status, $this->entity->getStatusId());
	}

	private function haveLead(array $attributes = []): int {
		if (!isset($attributes['status_id'])) {
			$attributes['status_id'] = $this->leadStatusForDialer;
		}
		return $this->leadFixtureHelper->haveLead($attributes);
	}

	private function haveLeadDialer(int $leadId, array $attributes = []): LeadDialer {
		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = static::TYPE_ACTIVE;
		}
		$attributes['lead_id'] = $leadId;
		return $this->tester->grabRecord(
			LeadDialer::class, [
				'id' => $this->tester->haveRecord(LeadDialer::class, $attributes),
			]
		);
	}

	protected function giveEntity(LeadDialer $dialer, array $config = []): void {
		$this->entity = new LeadDialerEntity($dialer, $config);
	}

}
