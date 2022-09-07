<?php

namespace backend\tests\Step\Functional;

use common\models\user\Worker;

class CzaterManager extends Manager {

	protected function getPermissions(): array {
		return [
			Worker::PERMISSION_CZATER,
		];
	}

}
