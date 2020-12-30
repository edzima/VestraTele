<?php

namespace frontend\tests\_support;

use common\models\user\User;

class PayReceivedTester extends IssueUserTester {

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [
				User::PERMISSION_PAY_RECEIVED,
			]
		);
	}
}
