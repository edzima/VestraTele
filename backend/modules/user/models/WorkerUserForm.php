<?php

namespace backend\modules\user\models;

use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;

class WorkerUserForm extends UserForm {

	protected const EXCLUDED_ROLES = [
		User::ROLE_ADMINISTRATOR,
		User::ROLE_BOOKKEEPER,
		Customer::ROLE_CUSTOMER,
		Customer::ROLE_HANDICAPPED,
		Customer::ROLE_SHAREHOLDER,
		Customer::ROLE_VICTIM,
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

	public int $status = User::STATUS_ACTIVE;

	protected function createModel(): Worker {
		return new Worker();
	}

	protected function applyAuth(int $id, bool $isNewRecord): void {
		if ($isNewRecord) {
			parent::applyAuth($id, $isNewRecord);
		}
	}

}
