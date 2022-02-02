<?php

namespace common\models\message;

class IssueSettlementCreateMessagesForm extends IssueSettlementMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'settlement',
			'create',
		];
	}

}
