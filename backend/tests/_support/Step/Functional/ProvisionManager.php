<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class ProvisionManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_PROVISION,
		];
	}

}
