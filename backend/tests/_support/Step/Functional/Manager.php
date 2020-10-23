<?php

namespace backend\tests\Step\Functional;

use backend\tests\FunctionalTester;
use common\models\user\User;

class Manager extends FunctionalTester {

	protected function getUsername(): string {
		return 'manager';
	}

	protected function getRoles(): array {
		return [
			User::ROLE_MANAGER,
		];
	}

}
