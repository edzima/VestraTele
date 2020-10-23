<?php

namespace backend\tests\Step\acceptance;

use backend\tests\AcceptanceTester;
use common\models\user\User;

class Admin extends AcceptanceTester {

	protected function getUsername(): string {
		return 'administrator';
	}

	protected function getRoles(): array {
		return [
			User::ROLE_ADMINISTRATOR,
		];
	}

}
