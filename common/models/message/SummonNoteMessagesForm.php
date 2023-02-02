<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\models\issue\Summon;
use Yii;

class SummonNoteMessagesForm extends IssueNoteMessagesForm {

	protected Summon $summon;

	protected static function mainKeys(): array {
		return [
			'issue',
			'note',
			'summon',
		];
	}

	public function setSummon(Summon $model): void {
		$this->summon = $model;
		if ($this->issue === null || $this->summon->issue_id !== $this->issue->getIssueId()) {
			$this->setIssue($model->issue);
		}
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseSummon($template);
	}

	protected function parseSummon(MessageTemplate $template) {
		$template->parseSubject([
			'summonTitle' => $this->summon->getTitleWithDocs(),
			'summonType' => $this->summon->getTypeName(),
		]);
		$template->parseBody([
			'summonTitle' => $this->summon->getTitleWithDocs(),
			'summonOwner' => $this->summon->owner->getFullName(),
			'summonStatus' => $this->summon->getStatusName(),
			'summonType' => $this->summon->getTypeName(),
			'summonDeadlineAt' => Yii::$app->formatter->asDate($this->summon->deadline_at),
		]);
	}

}
