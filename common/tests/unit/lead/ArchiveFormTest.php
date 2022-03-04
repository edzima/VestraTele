<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\ArchiveForm;
use common\modules\lead\models\LeadStatusInterface;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class ArchiveFormTest extends Unit {

	use UnitModelTrait;

	private ActiveLead $lead;
	private ArchiveForm $model;

	private LeadFixtureHelper $leadFixtureHelper;

	public const DEFAULT_USER_ID = 1;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
		);
	}

	public function _before() {
		parent::_before();
		$this->leadFixtureHelper = new LeadFixtureHelper($this->tester);
	}

	public function getModel(): ArchiveForm {
		return $this->model;
	}

	public function testEmpty(): void {
		$this->giveModel($this->giveLead());

		$this->thenUnsuccessValidate();
		$this->thenSeeError('User Id cannot be blank.', 'userId');
	}

	public function testSelfChangeAsEnable(): void {
		$this->giveModel($this->giveLead());

		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = true;
		$this->thenSuccessSave();

		$this->seeArchiveLead();
		$this->seeArchiveReport();
	}

	public function testSelfChangeAsDisable(): void {
		$this->giveModel($this->giveLead());

		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = false;
		$this->thenUnsuccessSave();

		$this->dontSeeArchiveLead();
		$this->dontSeeArchiveReport();
	}

	public function testSelfWithSameContactsAndType(): void {
		$lead1 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead2 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead3 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 3,
		]);

		$this->giveModel($lead1);
		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = true;
		$this->model->withSameContactWithType = true;
		$this->model->withSameContacts = true;
		$count = $this->model->save();
		$this->tester->assertSame(2, $count);

		$this->seeArchiveLead($lead1->getId());
		$this->seeArchiveLead($lead2->getId());
		$this->dontSeeArchiveLead($lead3->getId());

		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead1->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead2->getId());
		$this->dontSeeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead3->getId());
	}

	public function testSelfWithSameContactsAndNotType(): void {
		$lead1 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead2 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead3 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 3,
		]);

		$this->giveModel($lead1);
		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = true;
		$this->model->withSameContactWithType = false;
		$this->model->withSameContacts = true;
		$count = $this->model->save();
		$this->tester->assertSame(3, $count);

		$this->seeArchiveLead($lead1->getId());
		$this->seeArchiveLead($lead2->getId());
		$this->seeArchiveLead($lead3->getId());

		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead1->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead2->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead3->getId());
	}

	public function testDontSelfWithSameContactsAndNotType(): void {
		$lead1 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead2 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead3 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 3,
		]);

		$this->giveModel($lead1);
		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = false;
		$this->model->withSameContactWithType = false;
		$this->model->withSameContacts = true;

		$count = $this->model->save();
		$this->tester->assertSame(2, $count);

		$this->dontSeeArchiveLead($lead1->getId());
		$this->seeArchiveLead($lead2->getId());
		$this->seeArchiveLead($lead3->getId());

		$this->dontSeeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead1->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead2->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead3->getId());
	}

	public function testDontSelfWithSameContactsAndType(): void {
		$lead1 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead2 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 1,
		]);
		$lead3 = $this->giveLead(LeadStatusInterface::STATUS_NEW, [
			'source_id' => 3,
		]);

		$this->giveModel($lead1);
		$this->model->userId = static::DEFAULT_USER_ID;
		$this->model->selfChange = false;
		$this->model->withSameContactWithType = true;
		$this->model->withSameContacts = true;
		$this->thenSuccessSave();

		$this->dontSeeArchiveLead($lead1->getId());
		$this->seeArchiveLead($lead2->getId());
		$this->dontSeeArchiveLead($lead3->getId());

		$this->dontSeeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead1->getId());
		$this->seeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead2->getId());
		$this->dontSeeArchiveReport(LeadStatusInterface::STATUS_NEW, $lead3->getId());
	}

	private function seeArchiveLead(int $id = null): void {
		if ($id === null) {
			$id = $this->lead->getId();
		}
		$this->leadFixtureHelper->seeLead([
			'id' => $id,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function dontSeeArchiveLead(int $id = null) {
		if ($id === null) {
			$id = $this->lead->getId();
		}
		$this->leadFixtureHelper->dontSeeLead([
			'id' => $id,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function seeArchiveReport(int $oldStatus = LeadStatusInterface::STATUS_NEW, int $id = null) {
		if ($id === null) {
			$id = $this->lead->getId();
		}
		$this->leadFixtureHelper->seeReport([
			'lead_id' => $id,
			'old_status_id' => $oldStatus,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function dontSeeArchiveReport(int $oldStatus = LeadStatusInterface::STATUS_NEW, int $id = null) {
		if ($id === null) {
			$id = $this->lead->getId();
		}
		$this->leadFixtureHelper->dontSeeReport([
			'lead_id' => $id,
			'old_status_id' => $oldStatus,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function giveModel(ActiveLead $lead): void {
		$this->model = new ArchiveForm([
			'lead' => $lead,
		]);
	}

	private function giveLead(int $statusId = LeadStatusInterface::STATUS_NEW, array $attributes = []): ActiveLead {
		$attributes['status_id'] = $statusId;
		$this->lead = $this->leadFixtureHelper->grabLeadById(
			$this->leadFixtureHelper->haveLead($attributes)
		);
		return $this->lead;
	}
}
