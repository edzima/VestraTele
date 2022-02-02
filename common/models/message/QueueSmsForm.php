<?php

namespace common\models\message;

use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use yii\di\Instance;
use yii\queue\Queue;

abstract class QueueSmsForm extends SmsForm {

	/**
	 * @var string|array|Queue
	 */
	public $queue = 'queue';

	public function pushJob(): ?string {
		if (!$this->validate()) {
			return null;
		}

		return $this->getQueue()->push($this->createJob());
	}

	public function pushJobs(): ?array {
		if (!$this->validate()) {
			return null;
		}
		$ids = [];
		foreach ($this->getMessages() as $message) {
			$ids[] = $this->getQueue()->push($this->createJob($message));
		}
		return $ids;
	}

	private function getQueue(): Queue {
		if (!is_object($this->queue)) {
			$this->queue = Instance::ensure($this->queue, Queue::class);
		}
		return $this->queue;
	}

	abstract protected function createJob(MessageInterface $message = null): SmsSendJob;
}
