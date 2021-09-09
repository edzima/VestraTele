<?php

namespace common\models\issue;

use common\models\user\User;
use yii\base\Model;

/**
 * Class IssueNoteForm
 *
 * @property IssueNote $note
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class IssueNoteForm extends Model {

	public ?int $issue_id = null;
	public ?int $user_id = null;

	public ?string $type = null;
	public bool $is_pinned = false;
	public string $title = '';
	public string $description = '';
	public string $publish_at = '';

	public string $dateFormat = 'Y-m-d H:i';

	private ?IssueNote $model = null;

	public static function createSettlement(IssueSettlement $settlement) {
		$model = new static();
		$model->issue_id = $settlement->getIssueId();
		$model->type = IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, $settlement->getId());
		return $model;
	}

	public static function createSummon(Summon $summon) {
		$model = new static();
		$model->type = IssueNote::generateType(IssueNote::TYPE_SUMMON, $summon->id);
		$model->issue_id = $summon->getIssueId();
		return $model;
	}

	public function init(): void {
		parent::init();
		if (empty($this->publish_at)) {
			$this->publish_at = date($this->dateFormat);
		}
	}

	public function rules(): array {
		return [
			[['title', 'description', '!user_id', '!issue_id', 'publish_at'], 'required'],
			[['issue_id', 'user_id'], 'integer'],
			['is_pinned', 'boolean'],
			['!type', 'string'],
			[['title'], 'string', 'max' => 255],
			['description', 'string'],
			['publish_at', 'date', 'format' => 'php:' . $this->dateFormat],
			['issue_id', 'exist', 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array {
		return IssueNote::instance()->attributeLabels();
	}

	public function setModel(IssueNote $model): void {
		$this->model = $model;
		$this->issue_id = $model->issue_id;
		$this->is_pinned = $model->is_pinned;
		$this->title = $model->title;
		$this->type = $model->type;
		$this->description = $model->description;
		$this->publish_at = (string) $model->publish_at;
		$this->user_id = $model->user_id;
	}

	public function getModel(): IssueNote {
		if ($this->model === null) {
			$this->model = new IssueNote();
		}
		return $this->model;
	}

	public function save(): bool {
		if ($this->beforeSave()) {
			$model = $this->getModel();
			$model->issue_id = $this->issue_id;
			$model->is_pinned = $this->is_pinned;
			$model->user_id = $this->user_id;
			$model->type = $this->type;
			$model->title = $this->title;
			$model->description = $this->description;
			$model->publish_at = $this->publish_at;
			return $model->save();
		}
		return false;
	}

	protected function beforeSave(): bool {
		return $this->validate();
	}

}
