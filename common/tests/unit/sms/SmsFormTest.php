<?php

namespace common\tests\unit\sms;

use common\models\SmsForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use console\jobs\SmsSendJob;

abstract class SmsFormTest extends Unit {

	use UnitModelTrait;

	protected SmsForm $model;

	abstract protected function jobClass(): string;

	abstract protected function giveModel(array $config = []): void;

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Message cannot be blank.', 'message');
	}

	public function testPushJob(): void {
		$this->giveModel();
		$this->beforePushJob();
		$smsId = $this->model->pushJob();
		$this->tester->assertNotNull($smsId);
		$this->assertNotNull($this->tester->grabLastPushedJob());
		$this->afterPushJob();
	}

	protected function beforePushJob(): void {
		if (empty($this->model->message)) {
			$this->model->message = 'Test Push Job Message';
		}
		if (empty($this->model->phone)) {
			$this->model->phone = '48123123123';
		}
	}

	protected function afterPushJob(): void {
		/**
		 * @var SmsSendJob $job
		 */
		$job = $this->tester->grabLastPushedJob();
		$this->tester->assertInstanceOf(SmsSendJob::class, $job);
		$this->tester->assertInstanceOf($this->jobClass(), $job);
		$message = $job->message;
		$this->tester->assertSame($this->model->message, $message->getMessage());
		$this->tester->assertSame($this->model->phone, $message->getDst());
	}

	public function getModel(): SmsForm {
		return $this->model;
	}

}
