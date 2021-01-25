<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class WorkersManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_WORKERS,
		];
	}

}
