<?php

namespace backend\modules\user\models\search;

use common\models\user\query\UserQuery;
use common\models\user\Worker;

class WorkerUserSearch extends UserSearch {

	protected function createQuery(): UserQuery {
		return Worker::find();
	}

	public static function getRolesNames(): array {
		return [];
	}

	public static function getPermissionsNames(): array {
		return [];
	}
}
