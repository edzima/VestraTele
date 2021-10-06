<?php

namespace common\tests\unit\sms;

use common\models\SmsForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\queue\PushEvent;
use yii\queue\Queue;

abstract class SmsFormTest extends Unit {

	use UnitModelTrait;

	protected SmsForm $model;
	private ?PushEvent $event = null;
	protected MessageInterface $message;

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
		$this->model->pushJob();
		$this->assertNotNull($this->event);
		$this->afterPushJob();
	}

	protected function beforePushJob(): void {
		Yii::$app->queue->on(Queue::EVENT_AFTER_PUSH, function (PushEvent $event) {
			$this->event = $event;
			$job = $event->job;
			$this->tester->assertInstanceOf(SmsSendJob::class, $job);
			$this->message = $job->message;
		});
		if (empty($this->model->message)) {
			$this->model->message = 'Test Push Job Message';
		}
		if (empty($this->model->phone)) {
			$this->model->phone = '48123123123';
		}
	}

	protected function afterPushJob(): void {
		$this->tester->assertInstanceOf($this->jobClass(), $this->event->job);
		$this->tester->assertSame($this->model->message, $this->message->getMessage());
		$this->tester->assertSame($this->model->phone, $this->message->getDst());
	}

	public function getModel(): SmsForm {
		return $this->model;
	}

}
