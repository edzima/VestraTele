<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use Yii;
use yii\base\Model;

class IssueUserForm extends Model {

	public const SCENARIO_TYPE = 'type';
	public const SCENARIO_USER_LINK = 'user-link';

	public const SCENARIO_DEFAULT = self::SCENARIO_USER_LINK;

	protected const UNAVAILABLE_TYPES = [
		IssueUser::TYPE_CUSTOMER, IssueUser::TYPE_UNKNOWN, IssueUser::TYPE_LAWYER,
	];

	public $user_id;
	public $issue_id;
	public string $type = '';

	private ?User $_user = null;
	private ?Issue $_issue = null;

	public function scenarios(): array {
		$scenarios = parent::scenarios();
		$scenarios[static::SCENARIO_USER_LINK] = ['issue_id', 'type'];
		$scenarios[static::SCENARIO_TYPE] = ['type'];
		return $scenarios;
	}

	public function rules(): array {
		return [
			[['issue_id', 'user_id', 'type'], 'required'],
			[['issue_id', 'user_id'], 'integer'],
			['type', 'string'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			['issue_id', 'validateArchivedIssue'],
		];
	}

	public function attributeLabels(): array {
		return [
			'issue_id' => Yii::t('common', 'Issue'),
			'type' => Yii::t('common', 'As role'),
		];
	}

	public function validateArchivedIssue($attribute): void {
		if ($this->getIssue() && $this->getIssue()->isArchived()) {
			$this->addError($attribute, 'Issue cannot be archived.');
		}
	}

	public static function getTypesNames(): array {
		$types = IssueUser::getTypesNames();
		foreach (static::UNAVAILABLE_TYPES as $type) {
			unset($types[$type]);
		}
		return $types;
	}

	public function setIssue(Issue $model): void {
		$this->_issue = $model;
		$this->issue_id = $model->id;
	}

	public function getIssue(): ?Issue {
		if ($this->_issue === null) {
			$this->_issue = Issue::findOne($this->issue_id);
		}
		return $this->_issue;
	}

	public function getUser(): ?User {
		if ($this->_user === null) {
			$this->_user = User::findOne($this->user_id);
		}
		return $this->_user;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$issue = $this->getIssue();
		if (!$issue) {
			return false;
		}
		$issue->linkUser($this->getUser()->id, $this->type);
		$auth = Yii::$app->authManager;
		if (!$auth->checkAccess($this->getUser()->id, $this->type)) {
			$auth->assign($auth->getRole($this->type), $this->getUser()->id);
		}
		if (!$auth->checkAccess($this->getUser()->id, User::PERMISSION_ISSUE)) {
			$auth->assign($auth->getPermission(User::PERMISSION_ISSUE), $this->getUser()->id);
		}
		return true;
	}

}
