<?php

namespace common\models\message;

class IssuePayDelayedMessagesForm extends IssuePayMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'pay',
			'delayed',
		];
	}
}
