<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class IssueManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_ISSUE,
		];
	}

}
