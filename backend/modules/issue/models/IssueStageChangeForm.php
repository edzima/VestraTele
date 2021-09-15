<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use Yii;
use yii\base\Model;

class IssueStageChangeForm extends Model {

	public string $dateFormat = 'Y-m-d H:i:s';

	public int $stage_id;
	public string $date_at = '';

	public ?int $user_id = null;
	public ?string $description = null;

	private IssueInterface $issue;
	private int $old_stage_id;

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

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('common', 'Stage'),
			'date_at' => Yii::t('common', 'Date At'),
			'description' => Yii::t('common', 'Description'),
		];
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getIssue()->getIssueModel();
		$this->old_stage_id = $model->stage_id;
		$update = (bool) $model->updateAttributes([
			'stage_id' => $this->stage_id,
			'stage_change_at' => $this->date_at,
		]);
		return $update && $this->saveNote();
	}

	public function saveNote(): bool {
		$issueNote = new IssueNoteForm();
		$issueNote->type = IssueNote::generateType(
			IssueNote::generateType(IssueNote::TYPE_STAGE_CHANGE, $this->stage_id),
			$this->old_stage_id
		);
		$issueNote->issue_id = $this->getIssue()->getIssueId();
		$issueNote->user_id = $this->user_id;
		$issueNote->title = $this->getNoteTitle();
		$issueNote->description = $this->description;
		$issueNote->publish_at = $this->date_at;
		return $issueNote->save();
	}

	public function getStagesData(): array {
		$names = static::getStagesNames($this->getIssue()->getIssueType()->id);
		unset($names[$this->getIssue()->getIssueStage()->id]);
		return $names;
	}

	public static function getStagesNames(int $typeId): array {
		return IssueForm::getStages($typeId);
	}

	public function getIssue(): IssueInterface {
		return $this->issue;
	}

	public function getNoteTitle(): string {
		$names = static::getStagesNames($this->issue->getIssueType()->id);

		return Yii::t('issue', '{newStage} (previous: {previousStage})', [
			'newStage' => $names[$this->stage_id],
			'previousStage' => $names[$this->old_stage_id],
		]);
	}
}
