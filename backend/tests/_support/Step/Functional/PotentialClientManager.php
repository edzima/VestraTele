<?php

namespace backend\tests\Step\Functional;

use common\models\user\Worker;

class PotentialClientManager extends Manager {

	protected function getPermissions(): array {
		return [
			Worker::PERMISSION_POTENTIAL_CLIENT,
		];
	}

}
