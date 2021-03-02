<?php

namespace frontend\tests\Step\acceptance;

use frontend\tests\AcceptanceTester;
use common\models\user\User;

class Manager extends AcceptanceTester {

	protected function getUsername(): string {
		//@todo: fix permissions to use in MeetCalendarCest
		return 'administrator';
	}

	protected function getPassword(): string {
		return 'manager-passwd';
	}

	protected function getRoles(): array {
		return [
			User::ROLE_MANAGER,
		];
	}

}
