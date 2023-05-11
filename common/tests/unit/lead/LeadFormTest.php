<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadForm;
use common\tests\_support\UnitModelTrait;
use common\tests\helpers\LeadFactory;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadFormTest extends Unit {

	use UnitModelTrait;

	private LeadForm $model;

	private LeadFixtureHelper $fixtureHelper;

	public function _before() {
		parent::_before();
		$this->fixtureHelper = new LeadFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function testEmpty(): void {
		$this->giveModel([
		]);

		$this->thenUnsuccessValidate();

		$this->thenSeeError('Lead Name cannot be blank.', 'name');
		$this->thenSeeError('Source cannot be blank.', 'source_id');
		$this->thenSeeError('Status cannot be blank.', 'status_id');
		$this->thenSeeError('Date At cannot be blank.', 'date_at');

		$this->thenSeeError('Phone cannot be blank when email is blank.', 'phone');
		$this->thenSeeError('Email cannot be blank when phone is blank.', 'email');
	}

	public function testWithPhone(): void {
		$this->giveModel([
			'name' => 'Test lead',
			'phone' => '123-123-123',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);

		$this->thenSuccessValidate();
	}

	public function testUkrainePhoneNumber(): void {
		$this->giveModel([
			'name' => 'Test lead',
			'phone' => '+380 44 123-12-12',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);
		$model = $this->getModel();
		$model->phoneRegion = 'UA';

		$this->thenSuccessValidate();
	}

	public function testWithEmail(): void {
		$this->giveModel([
			'name' => 'Test lead',
			'email' => 'some@mail.com',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);

		$this->thenSuccessValidate();
	}

	public function testInvalidProvider(): void {
		$this->giveModel([
			'provider' => 'some-not-existed-provider',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Provider is invalid.', 'provider');
	}

	public function testWithOwner(): void {
		$this->giveModel([
			'name' => 'With owner',
			'email' => 'some@mail.com',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
			'owner_id' => 1,
		]);

		$this->thenSuccessValidate();

		$this->thenLeadIsForUser(1);
		$this->thenLeadIsNotForUser(2);
		$this->thenLeadIsNotForUser(null);
	}

	public function testWithOwnerWithSource(): void {
		$this->giveModel([
			'name' => 'Jonny',
			'email' => 'some@mail.com',
			'status_id' => 1,
			'source_id' => 2,
			'date_at' => '2020-01-01 12:00:00',
		]);
	}

	public function testUpdateLeadWithoutChangedContactAttributes(): void {
		$lead = $this->fixtureHelper->grabLeadById(
			$this->fixtureHelper->haveLead([
				'name' => 'Jonny',
				'email' => 'some@mail.com',
				'status_id' => 1,
				'source_id' => 2,
				'date_at' => '2020-01-01 12:00:00',
			])
		);

		$this->giveModel();
		$model = $this->getModel();
		$model->setLead($lead);
		$model->status_id = 2;

		$this->tester->assertTrue($model->updateLead($lead, 1));

		$this->fixtureHelper->seeLead([
			'id' => $lead->getId(),
			'status_id' => 2,
		]);
	}

	public function testUpdateLeadWithChangedContactAttributesEmail(): void {
		$lead = $this->fixtureHelper->grabLeadById(
			$this->fixtureHelper->haveLead([
				'name' => 'Jonny',
				'email' => 'some@mail.com',
				'status_id' => 1,
				'source_id' => 2,
				'date_at' => '2020-01-01 12:00:00',
			])
		);

		$this->giveModel();
		$model = $this->getModel();
		$model->setLead($lead);
		$model->status_id = 2;
		$model->email = 'updatedemail@test.com';

		$this->tester->assertTrue($model->updateLead($lead, 1));

		$this->fixtureHelper->seeLead([
			'id' => $lead->getId(),
			'status_id' => 2,
			'email' => 'updatedemail@test.com',
		]);

		$this->fixtureHelper->seeReport([
			'lead_id' => $lead->getId(),
		]);
		$report = $this->fixtureHelper->grabReport([
			'lead_id' => $lead->getId(),
			'owner_id' => 1,
		]);

		$this->tester->assertStringContainsString('Updated Contact Attributes!', $report->details);
		$this->tester->assertStringContainsString('Email is changed from: some@mail.com to updatedemail@test.com.', $report->details);
	}

	public function testUpdateLeadWithChangedContactAttributesPhone(): void {
		$lead = $this->fixtureHelper->grabLeadById(
			$this->fixtureHelper->haveLead([
				'name' => 'Jonny',
				'phone' => '123-123-123',
				'status_id' => 1,
				'source_id' => 2,
				'date_at' => '2020-01-01 12:00:00',
			])
		);

		$this->giveModel();
		$model = $this->getModel();
		$model->setLead($lead);
		$model->phone = '48123123222';

		$this->tester->assertTrue($model->updateLead($lead, 1));
		codecept_debug($model->getPhone());
		$this->fixtureHelper->seeLead([
			'id' => $lead->getId(),
			'phone' => '+48123123222',
		]);

		$this->fixtureHelper->seeReport([
			'lead_id' => $lead->getId(),
		]);
		$report = $this->fixtureHelper->grabReport([
			'lead_id' => $lead->getId(),
			'owner_id' => 1,
		]);

		$this->tester->assertStringContainsString('Updated Contact Attributes!', $report->details);
		$this->tester->assertStringContainsString('Phone is changed from: 123-123-123 to +48123123222.', $report->details);
	}

	public function testUpdateLeadWithChangedContactAttributesName(): void {
		$lead = $this->fixtureHelper->grabLeadById(
			$this->fixtureHelper->haveLead([
				'name' => 'Jonny',
				'email' => 'some@mail.com',
				'status_id' => 1,
				'source_id' => 2,
				'date_at' => '2020-01-01 12:00:00',
			])
		);

		$this->giveModel();
		$model = $this->getModel();
		$model->setLead($lead);
		$model->name = 'Erik';

		$this->tester->assertTrue($model->updateLead($lead, 1));

		$this->fixtureHelper->seeLead([
			'id' => $lead->getId(),
			'name' => 'Erik',
		]);

		$this->fixtureHelper->seeReport([
			'lead_id' => $lead->getId(),
		]);
		$report = $this->fixtureHelper->grabReport([
			'lead_id' => $lead->getId(),
			'owner_id' => 1,
		]);

		$this->tester->assertStringContainsString('Updated Contact Attributes!', $report->details);
		$this->tester->assertStringContainsString('Name is changed from: Jonny to Erik.', $report->details);
	}

	private function thenLeadIsForUser($id): void {
		$this->tester->assertTrue($this->getModel()->isForUser($id));
	}

	private function thenLeadIsNotForUser($id): void {
		$this->tester->assertFalse($this->getModel()->isForUser($id));
	}

	protected function giveModel(array $data = []): void {
		$this->model = new LeadForm($data);
	}

	public function getModel(): LeadForm {
		return $this->model;
	}
}
