<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\IssueNote;
use common\models\issue\IssueUser;

class IssueNoteMessagesForm extends IssueMessagesForm {

	protected IssueNote $note;

	protected static function mainKeys(): array {
		return [
			'issue',
			'note',
		];
	}

	public ?bool $sendSmsToCustomer = false;
	public ?bool $sendEmailToCustomer = false;
	public ?bool $sendSmsToAgent = false;
	public ?bool $sendEmailToWorkers = true;

	public $workersTypes = [
		IssueUser::TYPE_AGENT,
	];

	public function setNote(IssueNote $note): void {
		$this->note = $note;
		if ($this->issue === null || $this->note->issue_id !== $this->issue->getIssueId()) {
			$this->setIssue($note->issue);
		}
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseNote($template);
	}

	protected function parseNote(MessageTemplate $template) {
		$template->parseSubject([
			'noteTitle' => $this->note->title,
		]);
		$template->parseBody([
			'noteTitle' => $this->note->title,
			'noteDescription' => $this->note->description,
			'noteCreator' => $this->note->user->getFullName(),
		]);
	}

}
