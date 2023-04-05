<?php

namespace common\models\issue\form;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueStage;
use common\models\issue\IssueTagType;
use common\models\issue\IssueType;
use common\models\message\IssueStageChangeMessagesForm;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class IssueStageChangeForm extends Model {

	public string $dateFormat = 'Y-m-d H:i:s';

	public int $stage_id;
	public string $date_at = '';

	public ?int $user_id = null;
	public ?string $description = null;

	public $linkedIssues = [];

	public ?string $archives_nr = null;
	public bool $linkedIssuesMessages = true;

	private IssueInterface $issue;
	private int $previous_stage_id;
	private ?IssueStageChangeMessagesForm $_messagesForm = null;
	private ?IssueNote $note = null;

	public function __construct(IssueInterface $issue, array $config = []) {
		$this->setIssue($issue);
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['stage_id', 'date_at', '!user_id'], 'required'],
			[
				'archives_nr', 'required',
				'enableClientValidation' => false,
				'when' => function (): bool {
					return in_array($this->stage_id, IssueStage::ARCHIVES_IDS);
				},
			],
			['stage_id', 'compare', 'operator' => '!=', 'compareValue' => $this->getIssue()->getIssueStageId(), 'message' => Yii::t('issue', 'New Stage must be other than old.')],
			['stage_id', 'in', 'range' => array_keys($this->getStagesData())],
			[['description', 'archives_nr'], 'string'],
			['linkedIssuesMessages', 'boolean'],
			['date_at', 'date', 'format' => 'php:' . $this->dateFormat],
			[
				'linkedIssues',
				'in',
				'range' => array_keys($this->getLinkedIssuesNames()),
				'when' => function (): bool {
					return !empty($this->getLinkedIssuesNames());
				},
				'allowArray' => true,
			],
		];
	}

	public function getLinkedIssuesNames(): array {
		$names = [];
		foreach ($this->getIssue()->getIssueModel()->linkedIssues as $issue) {
			$names[$issue->getIssueId()] = $this->getLinkedIssueName($issue);
		}
		return $names;
	}

	public function getLinkedIssueName(IssueInterface $issue): string {
		$customerLinkedTags = IssueTagType::linkIssuesGridPositionFilter($issue->getIssueModel()->tags, IssueTagType::LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM);
		if (empty($customerLinkedTags)) {
			return strtr('{customer}:  {stage} - {issue}', [
				'{customer}' => $issue->getIssueModel()->customer,
				'{issue}' => $issue->getIssueName(),
				'{stage}' => $issue->getIssueStage()->name,
			]);
		}
		$tagsNames = [];
		foreach ($customerLinkedTags as $tag) {
			$tagsNames[] = $tag->name;
		}
		return strtr('{customer} ({tags}):  {stage} - {issue}', [
			'{customer}' => $issue->getIssueModel()->customer,
			'{issue}' => $issue->getIssueName(),
			'{stage}' => $issue->getIssueStage()->name,
			'{tags}' => implode(', ', $tagsNames),
		]);
	}

	public function attributeLabels(): array {
		return [
			'archives_nr' => Yii::t('common', 'Archives'),
			'stage_id' => Yii::t('common', 'Stage'),
			'date_at' => Yii::t('common', 'Date At'),
			'description' => Yii::t('common', 'Description'),
			'linkedIssues' => Yii::t('issue', 'Linked Issues'),
			'linkedIssuesMessages' => Yii::t('issue', 'Linked Issues Messages'),
		];
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName) && $this->getMessagesModel()->load($data, $formName);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getIssue()->getIssueModel();
		$this->previous_stage_id = $model->stage_id;
		$model->stage_id = $this->stage_id;
		$model->stage_change_at = $this->date_at;
		$model->archives_nr = $this->archives_nr;
		$model->generateStageDeadlineAt();
		$update = (bool) $model->updateAttributes([
			'stage_id',
			'stage_change_at',
			'stage_deadline_at',
			'archives_nr',
		]);
		return $update && $this->saveNote() && $this->saveLinked();
	}

	public function saveNote(): bool {
		$issueNote = $this->getNote();
		$issueNote->type = IssueNote::generateType(
			IssueNote::generateType(IssueNote::TYPE_STAGE_CHANGE, $this->stage_id),
			$this->previous_stage_id
		);
		$issueNote->issue_id = $this->getIssue()->getIssueId();
		$issueNote->user_id = $this->user_id;
		$issueNote->title = $this->getNoteTitle();
		$issueNote->description = $this->description;
		$issueNote->publish_at = $this->date_at;
		return $issueNote->save();
	}

	protected function getNote(): IssueNote {
		if ($this->note === null) {
			$this->note = new IssueNote();
		}
		return $this->note;
	}

	public function getStagesData(): array {
		$names = static::getStagesNames($this->getIssue()->getIssueType()->id);
		unset($names[$this->getIssue()->getIssueStage()->id]);
		asort($names);
		return $names;
	}

	public static function getStagesNames(int $typeId): array {
		$type = IssueType::get($typeId);
		if ($type === null) {
			return [];
		}
		$stages = ArrayHelper::map($type->stages, 'id', 'name');
		if ($type->parent_id) {
			$stages += static::getStagesNames($type->parent_id);
		}
		return $stages;
	}

	public function getIssue(): IssueInterface {
		return $this->issue;
	}

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		$this->stage_id = $issue->getIssueStageId();
		$this->archives_nr = $issue->getArchivesNr();
	}

	public function getNoteTitle(): string {
		$names = static::getStagesNames($this->issue->getIssueType()->id);

		return Yii::t('issue', '{newStage} (previous: {previousStage})', [
			'newStage' => $names[$this->stage_id],
			'previousStage' => $names[$this->previous_stage_id],
		]);
	}

	public function pushMessages(): bool {
		$message = $this->getMessagesModel();
		$message->withWithoutStageIdOnNotFound = true;
		$message->previousStage = IssueStage::getStages()[$this->previous_stage_id];
		$message->sms_owner_id = $this->user_id;
		return $message->pushMessages() > 0;
	}

	public function setMessagesModel(IssueStageChangeMessagesForm $messagesForm): void {
		$this->_messagesForm = $messagesForm;
		$this->_messagesForm->setIssue($this->issue);
	}

	public function getMessagesModel(): IssueStageChangeMessagesForm {
		if ($this->_messagesForm === null) {
			$this->_messagesForm = new IssueStageChangeMessagesForm([
				'issue' => $this->issue,
				'note' => $this->getNote(),
			]);
			$this->_messagesForm->setIssue($this->issue);
		}
		return $this->_messagesForm;
	}

	private function saveLinked(): bool {
		if (!empty($this->linkedIssues)) {

			/**
			 * @var IssueInterface[] $issues
			 */
			$issues = array_filter($this->getIssue()->getIssueModel()->linkedIssues, function (IssueInterface $issue): bool {
				return in_array($issue->getIssueId(), (array) $this->linkedIssues) && $issue->getIssueStageId() !== $this->stage_id;
			});

			foreach ($issues as $issue) {
				$model = new static($issue);
				$model->setAttributes($this->getAttributes(null, [
					'linkedIssues',
				]), false);
				$model->getMessagesModel()->setAttributes($this->getMessagesModel()->getAttributes());
				if ($model->save()) {
					if ($this->linkedIssuesMessages) {
						$model->pushMessages();
					}
				} else {
					Yii::warning(
						array_merge($model->getErrors(), [
							'issue_id' => $issue->getIssueId(),
						]), 'issue.stageChangeForm.linked');
				}
			}
		}
		return true;
	}
}
