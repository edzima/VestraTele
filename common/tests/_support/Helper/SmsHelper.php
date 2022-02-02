<?php

namespace common\tests\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Edzima\Yii2Adescom\BaseSmsSender;
use Edzima\Yii2Adescom\events\SMSEvent;
use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SenderInterface;
use yii\base\Event;
use yii\di\Instance;

class SmsHelper extends Module {

	/**
	 * @var string|array|BaseSmsSender
	 */
	public $sms = 'sms';

	private array $messages = [];
	private ?MessageInterface $lastMessage = null;

	public function onReconfigure() {
		parent::onReconfigure();
		$this->ensureSms();
		$this->detachEvents();
		$this->attachEvents();
		$this->clear();
	}

	private function ensureSms(): void {
		$this->sms = Instance::ensure($this->sms, SenderInterface::class);
	}

	public function _before(TestInterface $test) {
		$this->ensureSms();
		$this->clear();
		$this->attachEvents();
		parent::_before($test);
	}

	protected function clear(): void {
		$this->messages = [];
		$this->lastMessage = null;
	}

	public function _after(TestInterface $test) {
		$this->detachEvents();
		$this->clear();
		parent::_after($test);
	}

	protected function attachEvents(): void {
		if (is_object($this->sms)) {
			Event::on(get_class($this->sms), BaseSmsSender::EVENT_AFTER_SEND, [$this, 'afterSend']);
		}
	}

	protected function detachEvents(): void {
		if (is_object($this->sms)) {
			Event::off(get_class($this->sms), BaseSmsSender::EVENT_AFTER_SEND, [$this, 'afterSend']);
		}
	}

	public function afterSend(SMSEvent $event): void {
		$this->messages[] = $event->message;
		$this->lastMessage = $event->message;
	}

	public function grabLastSmsMessage(): ?MessageInterface {
		return $this->lastMessage;
	}

	public function seeSmsIsSend($num = null) {
		if ($num === null) {
			$this->assertNotEmpty($this->messages, 'SMS dont send');
			return;
		}
		$this->assertCount($num, $this->messages, 'number of send SMS is equal to ' . $num);
	}

	public function dontSeeSmsIsSend(): void {
		$this->assertEmpty($this->messages);
	}

}
