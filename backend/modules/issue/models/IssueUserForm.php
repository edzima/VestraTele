<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use yii\base\Model;

class IssueUserForm extends Model {

	public $user_id;
	public $issue_id;
	public string $type = IssueUser::TYPE_MINOR;

	private ?User $_user;
	private ?Issue $_issue;

	public function rules(): array {
		return [
			[['issue_id', 'user_id', 'type'], 'required'],
			[['issue_id', 'user_id'], 'integer'],
			['type', 'string'],
			['type', 'in', 'range' => static::getTypes()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	protected static function getTypes(): array {
		return [
			IssueUser::TYPE_VICTIM,
			IssueUser::TYPE_MINOR,
			IssueUser::TYPE_DIED,
			IssueUser::TYPE_SHAREHOLDER,
		];
	}

	public static function getTypesNames(): array {
		$types = static::getTypes();
		return array_filter(IssueUser::getTypesNames(), static function (string $type) use ($types): bool {
			return in_array($type, $types, true);
		}, ARRAY_FILTER_USE_KEY);
	}

	public function getIssue(): ?Issue {
		if ($this->_issue === null || $this->_issue->id !== $this->issue_id) {
			$this->_issue = Issue::findOne($this->issue_id);
		}
		return $this->_issue;
	}

	public function setIssue(Issue $model): void {
		$this->_issue = $model;
		$this->issue_id = $model->id;
	}

	public function getUser(): User {
		return $this->_user;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$issue = Issue::findOne($this->issue_id);
		if (!$issue) {
			return false;
		}
		$issue->linkUser($this->getUser()->id, $this->type);
		return true;
	}

}
