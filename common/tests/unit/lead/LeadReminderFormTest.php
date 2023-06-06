<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadReminderForm;
use common\modules\lead\models\LeadReminder;
use common\modules\reminder\models\Reminder;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadReminderFormTest extends Unit {

	use UnitModelTrait;

	private LeadReminderForm $model;

	private LeadFixtureHelper $leadFixtureHelper;

	public function _before() {
		parent::_before();
		$this->leadFixtureHelper = new LeadFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reminder(),
		);
	}

	public function getModel(): LeadReminderForm {
		return $this->model;
	}

	public function testWithoutUser(): void {
		$this->giveModel();
		$model = $this->model;
		$model->setLead($this->leadFixtureHelper->grabLeadById(1));
		$model->priority = Reminder::PRIORITY_LOW;
		$model->details = 'Test Lead Details';
		$model->date_at = '2020-01-01 10:15';
		$this->thenSuccessSave();
		$this->thenSeeReminder(
			Reminder::PRIORITY_LOW,
			'2020-01-01 10:15',
			'Test Lead Details'
		);
		$this->thenSeeLeadReminder(
			1,
			$model->getModel()->id,
		);
	}

	public function testUserFromLead(): void {
		$this->giveModel();
		$model = $this->model;
		$model->setLead($this->leadFixtureHelper->grabLeadById(1));
		$model->priority = Reminder::PRIORITY_LOW;
		$model->details = 'Test Lead Details';
		$model->date_at = '2020-01-01 10:15';
		$model->user_id = 1;
		$this->thenSuccessSave();
		$this->thenSeeReminder(
			Reminder::PRIORITY_LOW,
			'2020-01-01 10:15',
			'Test Lead Details',
			1
		);
		$this->thenSeeLeadReminder(
			1,
			$model->getModel()->id,
		);
	}

	public function testUserNotFromLead(): void {
		$this->giveModel();
		$model = $this->model;
		$model->setLead($this->leadFixtureHelper->grabLeadById(1));
		$model->priority = Reminder::PRIORITY_LOW;
		$model->details = 'Test Lead Details';
		$model->date_at = '2020-01-01 10:15';
		$model->user_id = 4;
		$this->thenUnsuccessSave();
		$this->thenSeeError('User is invalid.', 'user_id');
	}

	private function thenSeeReminder(int $priority, string $date_at, ?string $details = null, ?int $user_id = null) {
		$this->tester->seeRecord(Reminder::class, [
			'priority' => $priority,
			'date_at' => $date_at,
			'details' => $details,
			'user_id' => $user_id,
		]);
	}

	private function giveModel(array $config = []): void {
		$this->model = new LeadReminderForm($config);
	}

	private function thenSeeLeadReminder(int $lead_id, int $reminder_id) {
		$this->tester->seeRecord(LeadReminder::class, [
			'lead_id' => $lead_id,
			'reminder_id' => $reminder_id,
		]);
	}
}
