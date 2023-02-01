<?php

namespace common\models\message;

class UpdateNoteMessagesForm extends IssueNoteMessagesForm {

	public ?bool $sendEmailToWorkers = true;

	protected static function mainKeys(): array {
		return [
			'issue',
			'note',
			'update',
		];
	}

}
