<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\helpers\Html;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use Yii;
use yii\db\Expression;

class IssueStageChangeMessagesForm extends IssueMessagesForm {

	public const KEY_CUSTOMER = 'customer';
	public const KEY_WORKERS = 'workers';

	public const KEY_STAGE_ID = 'stageID';
	public const KEY_REMINDER_DAYS = 'reminderDays';

	public ?IssueNote $note = null;
	public ?IssueStage $previousStage = null;
	public ?string $stageChangeAt = null;

	protected static function mainKeys(): array {
		return [
			'issue',
			'stageChange',
		];
	}

	public bool $withDaysReminderKey = false;
	public bool $withStageIdKey = true;
	public bool $withWithoutStageIdOnNotFound = false;

	public ?bool $sendSmsToCustomer = false;
	public ?bool $sendEmailToCustomer = false;
	public ?bool $sendSmsToAgent = false;

	public $workersTypes = [
		IssueUser::TYPE_AGENT,
		IssueUser::TYPE_RECOMMENDING,
	];

	public static function pushDelayedMessages(int $smsOwnerId): array {
		$templates = static::daysReminderTemplates();

		$customers = $templates[static::KEY_CUSTOMER];
		$workers = $templates[static::KEY_WORKERS];

		$customersSMS = 0;
		$customersEmail = 0;
		$agentSMS = 0;
		$agentEmail = 0;
		foreach ($customers as $key => $template) {
			$issues = static::findIssues($key)->all();
			foreach ($issues as $issue) {
				$model = new static();
				$model->setIssue($issue);

				if (MessageTemplateKeyHelper::isSMS($key)) {
					$model->sendSmsToCustomer = true;
					$model->sendEmailToCustomer = false;
					$model->sms_owner_id = $smsOwnerId;
					$model->setCustomerSMSTemplate($template);
					$customersSMS += $model->pushCustomerMessages();
				} elseif (MessageTemplateKeyHelper::isEmail($key)) {
					$model->sendEmailToCustomer = true;
					$model->sendSmsToCustomer = false;
					$model->setCustomerEmailTemplate($template);
					$customersEmail += $model->pushCustomerMessages();
				}
			}
		}

		foreach ($workers as $key => $template) {
			$issues = static::findIssues($key)->all();
			foreach ($issues as $issue) {
				$model = new static();
				$model->setIssue($issue);
				if (MessageTemplateKeyHelper::isSMS($key)) {
					$model->sendSmsToAgent = true;
					$model->sendEmailToWorkers = false;
					$model->sms_owner_id = $smsOwnerId;
					$model->setAgentSMSTemplate($template);
					$model->pushWorkersMessages();
				} elseif (MessageTemplateKeyHelper::isEmail($key)) {
					$model->sendSmsToAgent = false;
					$model->sendEmailToWorkers = true;
					$model->workersTypes = [IssueUser::TYPE_AGENT];
					$model->setWorkersEmailTemplate($template);
					$agentEmail += $model->pushWorkersMessages();
				}
			}
		}

		return [
			'customersSMS' => $customersSMS,
			'customersEmail' => $customersEmail,
			'agentsSMS' => $agentSMS,
			'agentsEmail' => $agentEmail,
		];
	}

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
		$typesId = static::getTypesIds($key);
		$days = static::getDaysReminder($key);

		return Issue::find()
			->andFilterWhere(['stage_id' => $stageId])
			->andFilterWhere(['type_id' => $typesId])
			->andFilterWhere(['=', new Expression("DATEDIFF(CURDATE(), stage_change_at)"), $days]);
	}

	public static function getDaysReminder(string $key): ?int {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_REMINDER_DAYS);
	}

	public static function getStageID(string $key): ?int {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_STAGE_ID);
	}

	public const GROUP_CUSTOMER_SMS = 'customerSms';
	private const GROUP_CUSTOMER_EMAIL = 'customerEmail';
	public const GROUP_AGENT_SMS = 'agentSms';
	private const GROUP_WORKERS_EMAIL = 'workersEmail';

	private array $templateGroups = [];

	private const GROUP_KEY_WITHOUT_STAGE_ID = 'withoutStageId';

	public function getCustomerSmsMessages(): array {
		return $this->getStagesMessagesData(static::GROUP_CUSTOMER_SMS);
	}

	protected function getStagesMessagesData(string $groupType): array {
		$groups = $this->getTemplatesGroups($groupType);
		$stages = [];

		foreach ($groups as $key => $templates) {
			if (is_int($key)) {
				$template = reset($templates);
				if ($template) {
					$this->parseTemplate($template);
					$stages[] = [
						'stage_id' => $key,
						'message' => Html::encode($template->getSmsMessage()),
					];
				}
			}
		}
		return $stages;
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseNote($template);
	}

	protected function parseIssue(MessageTemplate $template): void {
		parent::parseIssue($template);
		$data = [
			'stage' => $this->issue->getIssueStage()->name,
			'agentPhone' => $this->issue->getIssueModel()->agent->getPhone(),
		];
		$stageChangeAt = $this->stageChangeAt ?: $this->issue->getIssueModel()->stage_change_at;
		if ($stageChangeAt) {
			$data['stageChangeAt'] = Yii::$app->formatter->asDate($stageChangeAt);
		}
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
		if ($this->issue && $this->withStageIdKey) {
			$parts[static::KEY_STAGE_ID] = $this->issue->getIssueStageId();
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
			$parts[] = static::issueTypesKeyPart($issueTypesIds);
		}
		return MessageTemplateKeyHelper::generateKey($parts);
	}

	protected function getAgentSMSTemplate(): ?MessageTemplate {
		if ($this->agentSMSTemplate) {
			return $this->agentSMSTemplate;
		}
		return $this->findFirstTemplate(
			$this->getTemplatesGroups(static::GROUP_AGENT_SMS)
		);
	}

	protected function getCustomerSMSTemplate(): ?MessageTemplate {
		if ($this->customerSMSTemplate) {
			return $this->customerSMSTemplate;
		}
		return $this->findFirstTemplate(
			$this->getTemplatesGroups(static::GROUP_CUSTOMER_SMS)
		);
	}

	protected function getCustomerEmailTemplate(): ?MessageTemplate {
		if ($this->customerEmailTemplate) {
			return $this->customerEmailTemplate;
		}
		return $this->findFirstTemplate(
			$this->getTemplatesGroups(static::GROUP_CUSTOMER_EMAIL)
		);
	}

	protected function getWorkersTemplate(): ?MessageTemplate {
		if ($this->workersEmailTemplate) {
			return $this->workersEmailTemplate;
		}
		return $this->findFirstTemplate(
			$this->getTemplatesGroups(static::GROUP_WORKERS_EMAIL)
		);
	}

	protected function findFirstTemplate(array $groupTemplates): ?MessageTemplate {
		$template = null;
		if ($this->withStageIdKey) {
			$stageTemplates = $groupTemplates[$this->issue->getIssueStageId()] ?? null;
			if (!empty($stageTemplates)) {
				if (count($stageTemplates) > 1) {
					Yii::warning('Find more than 1 templates: ' . count($stageTemplates) . ' for stage: ' . $this->issue->getIssueStageId(), __METHOD__);
				}
				$template = reset($stageTemplates);
			}
		}
		if (empty($template) && ($this->withWithoutStageIdOnNotFound || !$this->withStageIdKey)) {
			$withoutStageIdTemplates = $groupTemplates[static::GROUP_KEY_WITHOUT_STAGE_ID] ?? null;
			if (!empty($withoutStageIdTemplates)) {
				if (count($withoutStageIdTemplates) > 1) {
					Yii::warning('Find more than 1 templates without stage id.', __METHOD__);
				}
				$template = reset($withoutStageIdTemplates);
			}
		}
		return $template;
	}

	protected function getTemplatesGroups(string $group) {
		if (isset($this->templateGroups[$group])) {
			return $this->templateGroups[$group];
		}
		$currentWithStageIdKey = $this->withStageIdKey;
		if ($this->withStageIdKey) {
			$this->withStageIdKey = false;
		}
		$templates = [];
		switch ($group) {
			case static::GROUP_CUSTOMER_SMS:
				$templates = $this->getTemplates(static::TYPE_SMS, $this->getCustomerTemplateKey());
				break;
			case static::GROUP_CUSTOMER_EMAIL:
				$templates = $this->getTemplates(static::TYPE_EMAIL, $this->getCustomerTemplateKey());
				break;
			case static::GROUP_AGENT_SMS:
				$templates = $this->getTemplates(static::TYPE_SMS, $this->getWorkersTemplateKey());
				break;
			case static::GROUP_WORKERS_EMAIL:
				$templates = $this->getTemplates(static::TYPE_EMAIL, $this->getWorkersTemplateKey());
				break;
		}
		$this->withStageIdKey = $currentWithStageIdKey;
		if ($templates === null) {
			$templates = [];
		}
		if ($templates) {
			$templates = $this->groupTemplates($templates);
		}
		$this->templateGroups[$group] = $templates;
		return $templates;
	}

	/**
	 * @param MessageTemplate[] $templates
	 * @return MessageTemplate[][]
	 */
	protected function groupTemplates(array $templates): array {
		$groupTemplates = [];
		foreach ($templates as $template) {
			$stageId = static::getStageID($template->getKey());
			if ($stageId === null) {
				$groupTemplates[static::GROUP_KEY_WITHOUT_STAGE_ID][] = $template;
			} else {
				$groupTemplates[$stageId][] = $template;
			}
		}
		return $groupTemplates;
	}

}
