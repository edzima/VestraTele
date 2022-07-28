<?php

namespace common\models\issue\form;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueStage;
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

	public array $linkedIssues = [];

	private IssueInterface $issue;
	private int $previous_stage_id;
	private ?IssueStageChangeMessagesForm $_messagesForm = null;
	private ?IssueNote $note = null;

	public function __construct(IssueInterface $issue, array $config = []) {
		$this->issue = $issue;
		$this->stage_id = $issue->getIssueStage()->id;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['stage_id', 'date_at', '!user_id'], 'required'],
			['stage_id', 'compare', 'operator' => '!=', 'compareValue' => $this->getIssue()->getIssueStage()->id, 'message' => Yii::t('issue', 'New Stage must be other than old.')],
			['stage_id', 'in', 'range' => array_keys($this->getStagesData())],
			['description', 'string'],
			['date_at', 'date', 'format' => 'php:' . $this->dateFormat],
		];
	}

	public function getLinkedIssuesNames(): array {
		$names = [];
		foreach ($this->getIssue()->getIssueModel()->linkedIssues as $issue) {
			$names[$issue->getIssueId()] = $issue->getIssueName() . ' - ' . $issue->customer;
		}
		return $names;
	}

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('common', 'Stage'),
			'date_at' => Yii::t('common', 'Date At'),
			'description' => Yii::t('common', 'Description'),
			'linkedIssues' => Yii::t('issue', 'Linked Issues'),
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
		$update = (bool) $model->updateAttributes([
			'stage_id' => $this->stage_id,
			'stage_change_at' => $this->date_at,
		]);
		return $update && $this->saveNote();
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
		return ArrayHelper::map($type->stages, 'id', 'name');
	}

	public function getIssue(): IssueInterface {
		return $this->issue;
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
		$message->previousStage = IssueStage::getStages()[$this->previous_stage_id];
		return $message->pushMessages() > 0;
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
}
