<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\IssuePayInterface;
use Yii;

class IssuePayPayedMessagesForm extends IssuePayMessagesForm {

	public const KEY_PART_PAYMENT = 'partPayment';

	public bool $isPartPayment = false;

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'pay',
			'payed',
		];
	}

	public function keysParts(): array {
		$parts = parent::keysParts();
		if ($this->isPartPayment) {
			array_unshift($parts, static::KEY_PART_PAYMENT);
		}
		return $parts;
	}

}
