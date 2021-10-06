<?php

namespace common\models;

use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SmsForm as BaseSmsForm;
use Yii;

abstract class SmsForm extends BaseSmsForm {

	public function pushJob(): ?string {
		if (!$this->validate()) {
			return null;
		}
		return Yii::$app->queue->push($this->createJob());
	}

	public function pushJobs(): ?array {
		if (!$this->validate()) {
			return null;
		}
		$ids = [];
		foreach ($this->getMessages() as $message) {
			$ids[] = Yii::$app->queue->push($this->createJob($message));
		}
		return $ids;
	}

	abstract protected function createJob(MessageInterface $message = null): SmsSendJob;
}
