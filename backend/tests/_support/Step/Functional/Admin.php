<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class Admin extends UserRbac {

	protected const USERNAME = 'admin';
	protected const PASSWORD = 'password';

	protected function getRoles(): array {
		return [
			User::ROLE_ADMINISTRATOR,
		];
	}

}
