<?php

namespace frontend\tests\_support;

use common\models\user\Worker;

class CustomerServiceTester extends IssueUserTester {

	protected function getUsername(): string {
		return 'customer-service';
	}

	public function getRoles(): array {
		return [
			Worker::ROLE_CUSTOMER_SERVICE,
		];
	}

}
