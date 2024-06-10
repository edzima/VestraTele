<?php

namespace common\modules\file\models;

use common\models\user\User;
use Yii;
use yii\base\Model;

class IssueFileAccess extends Model {

	public ?int $user_id = null;

	private IssueFile $issueFile;

	private array $_users = [];

	public function rules(): array {
		return [
			['user_id', 'required'],
			['user_id', 'integer'],
			['user_id', 'in', 'range' => array_keys($this->getUsersNames())],
		];
	}

	public function __construct(IssueFile $issueFile, $config = []) {
		parent::__construct($config);
		$this->issueFile = $issueFile;
	}

	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('file', 'User'),
		];
	}

	public function getIssueFile(): IssueFile {
		return $this->issueFile;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = new FileAccess();
		$model->file_id = $this->issueFile->file_id;
		$model->user_id = $this->user_id;
		return $model->save(false);
	}

	public function getUsersNames(): array {
		if (empty($this->_users)) {
			$this->_users = User::getSelectList(User::getAssignmentIds([User::PERMISSION_ISSUE]));
			$currentUsers = $this->issueFile->file->getFileAccess()
				->select('user_id')
				->column();
			foreach ($currentUsers as $userId) {
				unset($this->_users[$userId]);
			}
		}
		return $this->_users;
	}
}
