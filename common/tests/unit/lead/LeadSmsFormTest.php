<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSmsForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

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
		$this->thenSeeError('Status cannot be current Status: New', 'status_id');
	}

	public function testPushJob(): void {
		$this->giveModel();
		$this->model->message = 'Test Message';
		$this->model->status_id = 2;
		$this->model->owner_id = 1;
		$this->thenSuccessValidate();
		$jobId = $this->model->pushJob();
		$this->tester->assertNotEmpty($jobId);
		$this->tester->assertNotEmpty(\Yii::$app->queue->status($jobId));
	}

	private function giveModel(int $lead_id = 1): void {
		$this->model = new LeadSmsForm($this->tester->grabRecord(Lead::class, ['id' => $lead_id]));
	}

	public function getModel(): LeadSmsForm {
		return $this->model;
	}
}
