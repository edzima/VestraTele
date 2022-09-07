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

	private LeadForm $lead;

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function testEmpty(): void {
		$this->giveLead([
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
		$this->giveLead([
			'name' => 'Test lead',
			'phone' => '123-123-123',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);

		$this->thenSuccessValidate();
	}

	public function testUkrainePhoneNumber():void{
		$this->giveLead([
			'name' => 'Test lead',
			'phone' => '+380 44 123-12-12',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);
		$this->lead->phoneRegion = 'UA';

		$this->thenSuccessValidate();
	}

	public function testWithEmail(): void {
		$this->giveLead([
			'name' => 'Test lead',
			'email' => 'some@mail.com',
			'status_id' => 1,
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);

		$this->thenSuccessValidate();
	}

	public function testInvalidProvider(): void {
		$this->giveLead([
			'provider' => 'some-not-existed-provider',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Provider is invalid.', 'provider');
	}

	public function testWithOwner(): void {
		$this->giveLead([
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
		$this->giveLead([
			'name' => 'Jonny',
			'email' => 'some@mail.com',
			'status_id' => 1,
			'source_id' => 2,
			'date_at' => '2020-01-01 12:00:00',
		]);
	}

	private function thenLeadIsForUser($id): void {
		$this->tester->assertTrue($this->lead->isForUser($id));
	}

	private function thenLeadIsNotForUser($id): void {
		$this->tester->assertFalse($this->lead->isForUser($id));
	}

	protected function giveLead(array $data): void {
		$this->lead = LeadFactory::createLead($data);
	}

	public function getModel(): Model {
		return $this->lead;
	}
}
