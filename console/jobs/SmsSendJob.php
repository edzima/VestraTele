<?php

namespace console\jobs;

use console\jobs\exceptions\NotSendSmsException;
use Edzima\Yii2Adescom\models\MessageInterface;
use Exception;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SmsSendJob extends BaseObject implements JobInterface {

	public MessageInterface $message;

	/**
	 * @throws NotSendSmsException
	 */
	public function execute($queue) {
		$this->run();
	}

	/**
	 * @return string
	 * @throws NotSendSmsException
	 */
	public function run(): string {
		try {
			$id = $this->message->send();
		} catch (Exception $exception) {
			throw new NotSendSmsException($exception->getMessage(), $exception->getCode());
		}
		if (empty($id)) {
			throw new NotSendSmsException("Sms Send don't return SMS_ID.");
		}
		return $id;
	}

}
