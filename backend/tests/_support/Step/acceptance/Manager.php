<?php

namespace backend\tests\Step\acceptance;

use backend\tests\AcceptanceTester;
use common\models\user\User;

class Manager extends AcceptanceTester {

	protected function getUsername(): string {
		return 'manager';
	}

	protected function getPassword(): string {
		return 'manager-passwd';
	}

	protected function getRoles(): array {
		return [
			User::ROLE_MANAGER,
		];
	}

}
