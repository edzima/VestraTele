<?php

namespace backend\modules\user\models;

use common\helpers\Inflector;
use common\models\user\Customer;
use Yii;

class CustomerUserForm extends UserForm {

	public bool $isEmailRequired = false;
	public bool $sendEmail = true;
	public $roles = [
		Customer::ROLE_CUSTOMER,
	];

	protected function createModel(): Customer {
		return new Customer();
	}

	public static function getRolesNames(): array {
		$rolesNames = parent::getRolesNames();
		return [
			Customer::ROLE_CUSTOMER => $rolesNames[Customer::ROLE_CUSTOMER],
			Customer::ROLE_VICTIM => $rolesNames[Customer::ROLE_VICTIM],
		];
	}

	public function beforeValidate(): bool {
		if ($this->scenario === static::SCENARIO_CREATE) {
			if (empty($this->username)) {
				$this->username = $this->generateUsername();
			}
			if (empty($this->password)) {
				$this->password = $this->generatePassword();
			}
		}
		return parent::beforeValidate();
	}

	public function generateUsername(): string {
		$profile = $this->getProfile();
		if ($profile->validate(['firstname', 'lastname'], false)) {
			return Inflector::transliterate(
				mb_substr($profile->firstname, 0, 1, Yii::$app->charset)
				. mb_substr($profile->lastname, 0, 1, Yii::$app->charset)
				. time()
			);
		}
		return '';
	}

	public function generatePassword(): string {
		$profile = $this->getProfile();
		if ($profile->validate('lastname', false)) {
			$profile = $this->getProfile();
			return strtoupper(Inflector::slug($profile->lastname, '-', false)) . date('Y');
		}

		return '';
	}

}
