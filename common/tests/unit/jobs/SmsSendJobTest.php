<?php

namespace common\tests\unit\jobs;

use common\tests\unit\Unit;
use console\jobs\exceptions\NotSendSmsException;
use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\BaseSmsSender;
use Edzima\Yii2Adescom\events\SMSEvent;
use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SenderInterface;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;

class SmsSendJobTest extends Unit {

	protected SmsSendJob $job;
	private SenderInterface $sender;
	private MessageInterface $message;

	protected const DEFAULT_MESSAGE_TEXT = 'Test Message';

	protected string $jobClass = SmsSendJob::class;

	public function _before() {
		parent::_before();
		$this->giveSender();
		$this->giveMessage([
			'message' => static::DEFAULT_MESSAGE_TEXT,
			'dst' => 'Test DST',
		]);
		$this->giveJob();
	}

	public function testExecuteWhenDontSend(): void {
		Event::on(BaseSmsSender::class, BaseSmsSender::EVENT_BEFORE_SEND, function (SMSEvent $event) {
			$event->isValid = false;
		});

		$this->tester->expectThrowable(NotSendSmsException::class, function () {
			$this->whenRun();
		});
	}

	public function testExecute(): void {
		$this->tester->assertNotEmpty($this->whenRun());
	}

	/**
	 * @return string
	 * @throws NotSendSmsException
	 */
	protected function whenRun(): string {
		return $this->job->run();
	}

	protected function giveJob(array $config = []): void {
		$this->job = $this->createJob($config);
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function createJob(array $config = []): SmsSendJob {
		$config['class'] = $this->jobClass;
		if (!isset($config['message'])) {
			$config['message'] = $this->message;
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($config);
	}

	protected function giveSender(): void {
		$this->sender = Yii::$app->sms;
	}

	protected function giveMessage(array $config): void {
		$this->message = $this->sender->compose($config);
	}
}
