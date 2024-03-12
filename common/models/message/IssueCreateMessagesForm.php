<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use Yii;

class IssueCreateMessagesForm extends IssueMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'create',
		];
	}

	protected function parseIssue(MessageTemplate $template): void {
		parent::parseIssue($template);
		$template->parseSubject([
			'issueCreatedAt' => Yii::$app->formatter->asDate($this->issue->getIssueName()),
		]);
		$template->parseBody([
			'issueCreatedAt' => Yii::$app->formatter->asDate($this->issue->getIssueName()),
		]);
	}
}
