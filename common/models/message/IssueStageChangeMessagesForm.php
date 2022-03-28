<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;

class IssueStageChangeMessagesForm extends IssueMessagesForm {

	public ?IssueStage $previousStage = null;

	protected static function mainKeys(): array {
		return [
			'issue',
			'stageChange',
		];
	}

	public ?bool $sendSmsToCustomer = false;
	public ?bool $sendEmailToCustomer = false;
	public ?bool $sendSmsToAgent = false;

	public $workersTypes = [
		IssueUser::TYPE_AGENT,
	];

	protected function parseIssue(MessageTemplate $template): void {
		parent::parseIssue($template);
		$data = [
			'stage' => $this->issue->getIssueStage()->name,
		];
		if ($this->previousStage) {
			$data['previousStage'] = $this->previousStage->name;
		}
		$template->parseSubject($data);
		$template->parseBody($data);
	}

}
