<?php

namespace frontend\tests\_support;

use common\models\user\User;
use frontend\tests\FunctionalTester;

class IssueUserTester extends FunctionalTester {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_ISSUE,
		];
	}
}
