<?php

namespace common\tests\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use yii\base\Event;
use yii\di\Instance;
use yii\queue\JobInterface;
use yii\queue\PushEvent;
use yii\queue\Queue;

class QueueHelper extends Module {

	/**
	 * @var string|array|Queue
	 */
	public $queue = 'queue';

	private array $pushEvents = [];
	private ?JobInterface $lastJob = null;

	public function onReconfigure() {
		parent::onReconfigure();
		$this->ensureQueue();
		$this->detachEvents();
		$this->attachEvents();
		$this->pushEvents = [];
		$this->lastJob = null;
	}

	private function ensureQueue(): void {
		$this->queue = Instance::ensure($this->queue, Queue::class);
	}

	public function _before(TestInterface $test) {
		$this->ensureQueue();
		$this->clear();
		$this->attachEvents();
		parent::_before($test);
	}

	protected function clear(): void {
		if ($this->queue instanceof \yii\queue\db\Queue) {
			$this->queue->clear();
		}
		$this->pushEvents = [];
		$this->lastJob = null;
	}

	public function _after(TestInterface $test) {
		$this->detachEvents();
		$this->clear();
		parent::_after($test);
	}

	protected function attachEvents(): void {
		if (is_object($this->queue)) {
			Event::on(get_class($this->queue), Queue::EVENT_AFTER_PUSH, [$this, 'onPushEvent']);
		}
	}

	protected function detachEvents(): void {
		if (is_object($this->queue)) {
			Event::off(get_class($this->queue), Queue::EVENT_AFTER_PUSH, [$this, 'onPushEvent']);
		}
	}

	public function onPushEvent(PushEvent $event): void {
		$this->debugSection('Queue', 'Push Job: #' . $event->id . ' to Queue:');
		$this->debug($event->job);
		$this->pushEvents[$event->id] = $event;
		$this->lastJob = $event->job;
	}

	public function seeQueueIsEmpty(): void {
		$this->assertEmpty($this->pushEvents);
	}

	public function grabLastPushedJob(): ?JobInterface {
		return $this->lastJob;
	}

	public function seeJobIsPushed($num = null) {
		if ($num === null) {
			$this->assertNotEmpty($this->pushEvents, 'jobs were pushed');
			return;
		}
		$this->assertCount($num, $this->pushEvents, 'number of pushed jobs is equal to ' . $num);
	}

	public function dontSeeJobIsPushed(): void {
		$this->assertEmpty($this->pushEvents);
	}

}
