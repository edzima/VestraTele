<?php

namespace backend\modules\user\models;

use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;

class WorkerUserForm extends UserForm {

	protected const EXCLUDED_ROLES = [
		User::ROLE_ADMINISTRATOR,
		User::ROLE_BOOKKEEPER,
		User::ROLE_USER,
		Customer::ROLE_CUSTOMER,
		Customer::ROLE_HANDICAPPED,
		Customer::ROLE_SHAREHOLDER,
		Customer::ROLE_VICTIM,
	];

	protected const EXCLUDED_PERMISSIONS = [
		User::PERMISSION_ARCHIVE,
		User::PERMISSION_CALCULATION_PAYS,
		User::PERMISSION_CALCULATION_PROBLEMS,
		User::PERMISSION_CALCULATION_TO_CREATE,
		User::PERMISSION_COST,
		User::PERMISSION_LOGS,
		User::PERMISSION_PAY,
		User::PERMISSION_PAYS_DELAYED,
		User::PERMISSION_PAY_RECEIVED,
		User::PERMISSION_PROVISION,
		User::PERMISSION_WORKERS,
	];

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
