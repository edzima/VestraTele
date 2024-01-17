<?php

namespace common\models\message;

use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\di\Instance;
use yii\queue\Queue;

abstract class QueueSmsForm extends SmsForm {

	/**
	 * @var string|array|Queue
	 */
	public $queue = 'queue';

	public $pushDelay;

	public $delayAt;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['pushDelay', 'integer', 'min' => 0],
			[
				'delayAt', 'date', 'format' => 'php:Y-m-d H:i',
				'min' => time(),
				'tooSmall' => Yii::t('common', 'Date At must be from future.'),
			],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
				'delayAt' => Yii::t('common', 'Push Delay At'),
			]
		);
	}

	public function pushJob(): ?string {
		if (!$this->validate()) {
			return null;
		}
		return $this->getQueue()
			->delay($this->getDelay())
			->push($this->createJob());
	}

	public function pushJobs(): ?array {
		if (!$this->validate()) {
			return null;
		}
		$ids = [];
		foreach ($this->getMessages() as $message) {
			$ids[] = $this->getQueue()
				->delay($this->getDelay())
				->push($this->createJob($message));
		}
		return $ids;
	}

	public function getDelay(): ?int {
		if (!empty($this->pushDelay)) {
			return $this->pushDelay;
		}
		if (!empty($this->delayAt)) {
			$delay = strtotime($this->delayAt) - time();
			if ($delay > 0) {
				return $delay;
			}
		}
		return null;
	}

	private function getQueue(): Queue {
		if (!is_object($this->queue)) {
			$this->queue = Instance::ensure($this->queue, Queue::class);
		}

		return $this->queue;
	}

	abstract protected function createJob(MessageInterface $message = null): SmsSendJob;
}
