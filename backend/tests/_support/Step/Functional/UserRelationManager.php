<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class UserRelationManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_USER_RELATION,
		];
	}

}
