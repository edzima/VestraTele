<?php

namespace backend\modules\user\models;

use common\models\user\Customer;

class CustomerUserForm extends UserForm {

	public bool $isEmailRequired = false;

	public $roles = [
		Customer::ROLE_CLIENT,
	];

	protected function createModel(): Customer {
		return new Customer();
	}

	public static function getRolesNames(): array {
		return [parent::getRolesNames()[Customer::ROLE_CLIENT]];
	}

	public function beforeValidate(): bool {
		if ($this->scenario === static::SCENARIO_CREATE) {
			$this->username = $this->generateUsername();
			$this->password = $this->generatePassword();
		}
		return parent::beforeValidate();
	}

	public function generateUsername(): string {
		$profile = $this->getProfile();
		if ($profile->validate(['firstname', 'lastname'], false) && !$profile->hasErrors(['firstname', 'lastname'])) {
			return $profile->firstname[0] . $profile->lastname[0] . time();
		}
		return '';
	}

	public function generatePassword(): string {
		$profile = $this->getProfile();
		if ($profile->validate('lastname', false) && !$profile->hasErrors('lastname')) {
			$profile = $this->getProfile();
			return $profile->lastname . time();
		}

		return '';
	}

}
