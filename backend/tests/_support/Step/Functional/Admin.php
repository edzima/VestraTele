<?php

namespace backend\tests\Step\Functional;

use backend\tests\FunctionalTester;
use common\models\user\User;

class Admin extends FunctionalTester {

	protected function getUsername(): string {
		return 'administrator';
	}

	protected function getRoles(): array {
		return [
			User::ROLE_ADMINISTRATOR,
		];
	}

}
