<?php

namespace common\fixtures\hint;

use common\fixtures\user\RbacUserFixture;
use common\models\user\User;

class HintUserFixture extends RbacUserFixture {

	public array $permissions = [
		User::PERMISSION_HINT,
	];
}
