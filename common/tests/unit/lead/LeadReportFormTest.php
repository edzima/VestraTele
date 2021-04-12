<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadReportForm;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\tests\unit\Unit;

class LeadReportFormTest extends Unit {

	private int $owner_id = 1;

	private ActiveLead $lead;

	private LeadReportForm $form;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		);
	}

	public function testSchemasData(): void {
		$this->givenLead('new-without-owner');
		$this->givenForm();

		$data = $this->form->getSchemaData();
		$this->tester->assertArrayHasKey(1, $data);
		$this->tester->assertArrayHasKey(2, $data);
		$this->tester->assertArrayNotHasKey(3, $data);

		$this->form->status_id = LeadStatusInterface::STATUS_ARCHIVE;
		$data = $this->form->getSchemaData();
		$this->tester->assertArrayNotHasKey(1, $data);
		$this->tester->assertArrayNotHasKey(2, $data);
		$this->tester->assertArrayHasKey(3, $data);
	}

	public function testEmpty(): void {
		$this->givenLead();
		$this->givenForm();
		$this->thenUnsuccessSave();
		$this->thenHasError('Schema Id cannot be blank.', 'schema_id');
	}

	public function testReportWithUpdateStatus(): void {
		$this->givenLead();
		$this->givenForm();
		$this->whenSchema(1);
		$this->thenSuccessSave();
		$this->thenSeeLead([
			'status_id' => $this->lead->getStatusId(),
			'source_id' => $this->lead->getSourceId(),
			'type_id' => $this->lead->getTypeId(),
		]);
		$this->thenSeeReport([
			'schema_id' => 1,
			'lead_id' => $this->lead->getId(),
		]);
	}

	public function testReportWithChangeStatus(): void {
		$this->givenLead();
		$this->givenForm();
		$this->whenSchema(3);
		$this->whenStatus(LeadStatusInterface::STATUS_ARCHIVE);
		$oldStatus = $this->lead->getStatusId();
		$this->thenSuccessSave();
		$this->thenSeeLead([
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'source_id' => $this->lead->getSourceId(),
			'type_id' => $this->lead->getTypeId(),
		]);
		$this->thenSeeReport([
			'schema_id' => 3,
			'owner_id' => $this->owner_id,
			'lead_id' => $this->lead->getId(),
			'old_status_id' => $oldStatus,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function givenLead(string $index = 'new-without-owner'): void {
		$this->lead = $this->tester->grabFixture(LeadFixtureHelper::LEAD, $index);
	}

	private function givenForm(): void {
		$this->form = new LeadReportForm($this->owner_id, $this->lead);
	}

	private function whenSchema(int $schemaId): void {
		$this->form->schema_id = $schemaId;
	}

	private function whenStatus(int $statusId) {
		$this->form->status_id = $statusId;
	}

	private function thenUnsuccessSave(): void {
		$this->tester->assertFalse($this->form->save());
	}

	private function thenHasError(string $message, string $attribute): void {
		$this->tester->assertSame($message, $this->form->getFirstError($attribute));
	}

	private function thenSuccessSave(): void {
		$this->tester->assertTrue($this->form->save());
	}

	private function thenSeeLead(array $attributes): void {
		$this->tester->seeRecord(Lead::class, $attributes);
	}

	private function thenSeeReport(array $attributes): void {
		$this->tester->seeRecord(LeadReport::class, $attributes);
	}

}
