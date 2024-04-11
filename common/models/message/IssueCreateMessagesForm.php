<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use Yii;
use yii\db\Expression;

class IssueCreateMessagesForm extends IssueMessagesForm {

	protected static function mainKeys(): array {
		return [
			'issue',
			'create',
		];
	}

	protected function parseIssue(MessageTemplate $template): void {
		parent::parseIssue($template);
		$createdAt = time();
		if (!empty($this->issue->getIssueModel()->created_at) && !$this->issue->getIssueModel()->created_at instanceof Expression) {
			$createdAt = $this->issue->getIssueModel()->created_at;
		}
		$template->parseSubject([
			'issueCreatedAt' => Yii::$app->formatter->asDate($createdAt),
		]);
		$template->parseBody([
			'issueCreatedAt' => Yii::$app->formatter->asDate($createdAt),
		]);
	}
}
