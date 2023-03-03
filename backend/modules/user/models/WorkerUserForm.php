<?php

namespace backend\modules\user\models;

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

	public int $status = User::STATUS_ACTIVE;

	protected function createModel(): Worker {
		return new Worker();
	}


}
