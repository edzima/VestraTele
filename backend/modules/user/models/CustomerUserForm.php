<?php

namespace backend\modules\user\models;

use common\models\user\Customer;

class CustomerUserForm extends UserForm {

	public int $status = Customer::STATUS_INACTIVE;

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

	private function hasValidFirstAndLastName(): bool {
		return $this->getProfile()->validate(['firstname', 'lastname']);
	}

	public function generateUsername(): string {
		if ($this->hasValidFirstAndLastName()) {
			$profile = $this->getProfile();
			return $profile->firstname . $profile->lastname . time();
		}

		return '';
	}

	public function generatePassword(): string {
		if ($this->hasValidFirstAndLastName()) {
			$profile = $this->getProfile();
			return $profile->firstname . $profile->lastname . time();
		}

		return '';
	}

}
