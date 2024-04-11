<?php

namespace backend\modules\user\models;

use common\helpers\Inflector;
use common\models\user\User;
use common\models\user\Worker;

class WorkerUserForm extends UserForm {

	protected const EXCLUDED_ROLES = [
		User::ROLE_ADMINISTRATOR,
		User::ROLE_BOOKKEEPER,
	];

	protected const EXCLUDED_PERMISSIONS = [
		User::PERMISSION_PROVISION,
		Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE,
	];

	public function rules(): array {
		return array_merge([
			['roles', 'required'],
		], parent::rules()
		);
	}

	protected function createModel(): Worker {
		return new Worker();
	}

	public function beforeValidate(): bool {
		if ($this->scenario === static::SCENARIO_CREATE) {
			if (empty($this->username)) {
				$this->username = $this->generateUsername();
			}
			if (empty($this->password)) {
				$this->password = $this->generatePassword();
			}
			$this->sendEmail = true;
		}
		return parent::beforeValidate();
	}

	public function generateUsername(): string {
		$profile = $this->getProfile();
		if ($profile->validate(['firstname', 'lastname'], false)) {
			$name = ucfirst(Inflector::slug($profile->firstname))
				. ucfirst(Inflector::slug($profile->lastname));
			$count = User::find()
				->select('count(*)')
				->andWhere(['like', 'username', $name])
				->count();
			if ($count) {
				$name .= $count + 1;
			}
			return $name;
		}
		return '';
	}

	public function generatePassword(): string {
		$profile = $this->getProfile();
		if ($profile->validate('lastname', false)) {
			$profile = $this->getProfile();
			return strtoupper(Inflector::slug($profile->lastname, '-', false)) . time();
		}

		return '';
	}

}
