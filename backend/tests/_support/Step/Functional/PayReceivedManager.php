<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class PayReceivedManager extends Manager {

	protected function getUsername(): string {
		return 'pay-received-manager';
	}

	protected function getPermissions(): array {
		return [
			User::PERMISSION_PAY_RECEIVED,
		];
	}

}
