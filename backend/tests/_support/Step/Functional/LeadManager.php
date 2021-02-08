<?php

namespace backend\tests\Step\Functional;

use common\models\user\Worker;

class LeadManager extends Manager {

	protected function getPermissions(): array {
		return [
			Worker::PERMISSION_LEAD,
		];
	}

}
