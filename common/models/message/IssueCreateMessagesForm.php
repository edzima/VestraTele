<?php

namespace common\models\message;

class IssueCreateMessagesForm extends IssueMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'create',
		];
	}
}
