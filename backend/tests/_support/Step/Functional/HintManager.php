<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class HintManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_HINT,
		];
	}

}
