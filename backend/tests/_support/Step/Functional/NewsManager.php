<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class NewsManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_NEWS_MANAGER,
		];
	}

}
