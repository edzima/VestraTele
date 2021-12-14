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
		User::ROLE_MANAGER,
		User::ROLE_CUSTOMER_SERVICE,
		Customer::ROLE_CUSTOMER,
		Customer::ROLE_HANDICAPPED,
		Customer::ROLE_SHAREHOLDER,
		Customer::ROLE_VICTIM,
	];

	protected const EXCLUDED_PERMISSIONS = [
		'loginToBackend',
		Worker::PERMISSION_CZATER,
		Worker::PERMISSION_ISSUE_DELETE,
		Worker::PERMISSION_MESSAGE_TEMPLATE,
		User::PERMISSION_ARCHIVE,
		User::PERMISSION_EXPORT,
		User::PERMISSION_CALCULATION_PAYS,
		User::PERMISSION_CALCULATION_PROBLEMS,
		User::PERMISSION_CALCULATION_TO_CREATE,
		User::PERMISSION_COST,
		Worker::PERMISSION_COST_DEBT,
		User::PERMISSION_LOGS,
		User::PERMISSION_PAY,
		Worker::PERMISSION_PAY_UPDATE,
		Worker::PERMISSION_PAY_PAID,
		Worker::PERMISSION_PAYS_DELAYED,
		Worker::PERMISSION_PAY_PART_PAYED,
		Worker::PERMISSION_PAY_RECEIVED,
		User::PERMISSION_PROVISION,
		Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE,
		Worker::PERMISSION_SUMMON_MANAGER,
		User::PERMISSION_WORKERS,
		Worker::PERMISSION_MULTIPLE_SMS,
		Worker::PERMISSION_SMS,
		Worker::PERMISSION_NOTE_TEMPLATE,
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
