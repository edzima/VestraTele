<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMultipleSmsForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use console\jobs\LeadSmsSendJob;

class LeadMultipleSmsFormTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_OWNER_ID = 1;

	private LeadMultipleSmsForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->model->owner_id = null;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Message cannot be blank.', 'message');
		$this->thenSeeError('Owner cannot be blank.', 'owner_id');
		$this->thenSeeError('Status cannot be blank.', 'status_id');
		$this->thenSeeError('Ids cannot be blank when Models are empty.', 'ids');
		$this->thenSeeError('Models cannot be blank when Ids are empty.', 'models');
	}

	public function testInvalidStatus(): void {
		$this->giveModel([
			'status_id' => -2,
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Status is invalid.', 'status_id');
	}

	public function testFromIds(): void {
		$this->giveModel([
			'ids' => [1, $this->tester->grabRecord(Lead::class, ['phone' => null]), 5],
			'status_id' => 2,
			'message' => 'Message for #1 and #2',
		]);

		$this->thenSuccessValidate();
		$jobs = $this->model->pushJobs();
		$this->tester->assertCount(2, $jobs);
		foreach ($this->model->getMessages() as $message) {
			$this->tester->assertTrue(
				$message->getDst() === '48777222122' ||
				$message->getDst() === '48555222111');
		}
		$job = $this->tester->grabLastPushedJob();

		$this->tester->assertInstanceOf(LeadSmsSendJob::class, $job);
		$this->tester->assertSame($job->lead_id, 5);
	}

	public function testFromModels(): void {
		$leadWithPhone = $this->tester->grabFixture(LeadFixtureHelper::LEAD, 'new-wordpress-accident');
		$leadWithoutPhone = $this->tester->grabFixture(LeadFixtureHelper::LEAD, 'new-without-phone-and-owner_and_source-without-phone');
		$leads = [];
		$leads[] = $leadWithoutPhone;
		$leads[] = $leadWithPhone;
		$this->giveModel([
			'status_id' => 2,
			'message' => 'Message for #1 and #2',
		]);
		$this->model->models = $leads;
		$this->thenSuccessValidate();
		$jobs = $this->model->pushJobs();
		$this->tester->assertCount(1, $jobs);
		$job = $this->tester->grabLastPushedJob();
		$this->tester->assertInstanceOf(LeadSmsSendJob::class, $job);
		$this->tester->assertSame($job->lead_id, 1);
	}

	private function giveModel(array $config = []): void {
		if (!isset($config['owner_id'])) {
			$config['owner_id'] = static::DEFAULT_OWNER_ID;
		}
		$this->model = new LeadMultipleSmsForm($config);
	}

	public function getModel(): LeadMultipleSmsForm {
		return $this->model;
	}
}
