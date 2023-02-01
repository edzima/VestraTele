<?php

namespace common\models\message;

use common\components\message\MessageTemplate;

class UpdateNoteMessagesForm extends IssueNoteMessagesForm {
	
	protected static function mainKeys(): array {
		return [
			'issue',
			'note',
			'update',
		];
	}

	protected function parseNote(MessageTemplate $template) {
		parent::parseNote($template);
		$template->parseSubject([
			'noteUpdater' => $this->note->updater->getFullName(),
		]);
		$template->parseBody([
			'noteUpdater' => $this->note->updater->getFullName(),
		]);
	}

}
