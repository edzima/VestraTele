<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;

class IssueStageChangeMessagesForm extends IssueMessagesForm {

	public const KEY_CUSTOMER = 'customer';
	public const KEY_WORKERS = 'workers';

	public const KEY_STAGE_ID = 'stageID';
	public const KEY_REMINDER_DAYS = 'reminderDays';

	public ?IssueNote $note = null;
	public ?IssueStage $previousStage = null;

	protected static function mainKeys(): array {
		return [
			'issue',
			'stageChange',
		];
	}

	public bool $withDaysReminderKey = false;

	public ?bool $sendSmsToCustomer = false;
	public ?bool $sendEmailToCustomer = false;
	public ?bool $sendSmsToAgent = false;

	public $workersTypes = [
		IssueUser::TYPE_AGENT,
	];

	/**
	 * @param string|null $language
	 * @return MessageTemplate[][]
	 */
	public static function daysReminderTemplates(string $language = null): array {
		$model = new static();
		$model->language = $language;
		$model->withDaysReminderKey = true;
		return [
			static::KEY_CUSTOMER => (array) $model
				->getTemplateManager()
				->getTemplatesLikeKey(static::generateKey('', $model->getCustomerTemplateKey()), $model->language),
			static::KEY_WORKERS => (array) $model
				->getTemplateManager()
				->getTemplatesLikeKey(static::generateKey('', $model->getWorkersTemplateKey()), $model->language),
		];
	}

	public static function findIssues(string $key): IssueQuery {
		$stageId = static::getStageID($key);
		$days = static::getDaysReminder($key);

		return Issue::find()
			->andFilterWhere(['stage_id' => $stageId]);
	}

	public static function getDaysReminder(string $key): ?int {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_REMINDER_DAYS);
	}

	public static function getStageID(string $key): ?int {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_STAGE_ID);
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseNote($template);
	}

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

	protected function parseNote(MessageTemplate $template) {
		if ($this->note) {
			$template->parseBody([
				'noteTitle' => $this->note->title,
				'noteDescription' => $this->note->description,
			]);
		}
	}

	public function keysParts(string $type): array {
		$parts = parent::keysParts($type);
		if ($this->withDaysReminderKey) {
			$parts[] = static::KEY_REMINDER_DAYS;
		}
		if ($this->issue) {
			$parts[static::KEY_STAGE_ID] = $this->issue->getIssueStage()->id;
		}
		return $parts;
	}

	public static function generateKey(string $type, string $key, ?array $issueTypesIds = null, ?int $stageId = null, ?int $daysReminder = null): string {
		$parts = array_merge((array) $type, static::mainKeys(), (array) $key);

		if ($daysReminder) {
			$parts[static::KEY_REMINDER_DAYS] = $daysReminder;
		}
		if ($stageId !== null) {
			$parts[static::KEY_STAGE_ID] = $stageId;
		}

		if (!empty($issueTypesIds)) {
			$parts[] = MessageTemplateKeyHelper::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}
}
