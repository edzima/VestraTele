<?php

namespace backend\modules\user\models;

use common\helpers\Inflector;
use common\models\user\Customer;
use common\models\user\User;
use Yii;

class CustomerUserForm extends UserForm {

	public bool $isEmailRequired = false;
	public bool $sendEmail = true;

	public $roles = [
		Customer::ROLE_CUSTOMER,
	];

	public $permissions = [
		Customer::PERMISSION_SUMMON,
	];

	protected const EXCLUDED_ROLES = [
		User::ROLE_ADMINISTRATOR,
		User::ROLE_MANAGER,
		User::ROLE_LAWYER,
	];

	protected function createModel(): Customer {
		return new Customer();
	}

	protected function applyAuth(int $id, bool $isNewRecord): void {
		if ($isNewRecord) {
			parent::applyAuth($id, $isNewRecord);
		}
	}

	public static function getRolesNames(): array {
		$rolesNames = parent::getRolesNames();
		foreach (static::EXCLUDED_ROLES as $role) {
			unset($rolesNames[$role]);
		}
		return $rolesNames;
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
