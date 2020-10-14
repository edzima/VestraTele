<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class Manager extends UserRbac {

	protected const USERNAME = 'manager';
	protected const PASSWORD = 'password';

	protected function getRoles(): array {
		return [
			User::ROLE_MANAGER,
		];
	}

}
