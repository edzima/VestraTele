<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSmsForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;

class LeadSmsFormTest extends Unit {

	use UnitModelTrait;

	private LeadSmsForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Owner Id cannot be blank.', 'owner_id');
		$this->thenSeeError('Message cannot be blank.', 'message');
	}

	public function testEmptyOnChangeStageScenario(): void {
		$this->giveModel();
		$this->model->scenario = LeadSmsForm::SCENARIO_CHANGE_STATUS;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Status cannot be current Status: New', 'status_id');
	}

	public function testInvalidStatus(): void {
		$this->giveModel();
		$this->model->status_id = 1010010100;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Status is invalid.', 'status_id');
	}

	public function testPushJob(): void {
		$this->giveModel();
		$this->model->message = 'Test Message';
		$this->model->status_id = 2;
		$this->model->owner_id = 1;
		$this->thenSuccessValidate();
		$jobId = $this->model->pushJob();
		$this->tester->assertNotEmpty($jobId);
		$this->tester->assertNotEmpty(Yii::$app->queue->status($jobId));
	}

	public function testDelayDate(): void {
		$this->giveModel();
		$this->tester->assertNull($this->model->getDelay());
		$this->model->delayAt = date(DATE_ATOM, strtotime('+ 1 hours'));
		$delay = $this->model->getDelay();
		$this->tester->assertNotEmpty($delay);
		$this->tester->assertTrue($delay > 3540 && $delay <= 3600);

		$this->model->delayAt = date(DATE_ATOM, strtotime('+ 30 minutes'));

		$delay = $this->model->getDelay();
		$this->tester->assertTrue($delay > 1750 && $delay <= 1800);
	}

	public function testDelayDateFromPast(): void {
		$this->giveModel();
		$this->tester->assertNull($this->model->getDelay());
		$this->model->delayAt = date('Y-m-d H:i', strtotime('- 1 hours'));
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Date At must be from future.', 'delayAt');
	}

	private function giveModel(int $lead_id = 1): void {
		$this->model = new LeadSmsForm($this->tester->grabRecord(Lead::class, ['id' => $lead_id]));
	}

	public function getModel(): LeadSmsForm {
		return $this->model;
	}
}
