<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class AdescomSmsCest {

	protected const ROUTE_PUSH = '/adescom-sms/send/push';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_PUSH);
		$I->dontSeeMenuLink('SMS');
		$I->seeResponseCodeIs(403);
	}

	public function checkWithSMSPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH);
		$I->seeMenuLink('SMS');
		$I->clickMenuSubLink('Send SMS');
		$I->seeInCurrentUrl(static::ROUTE_PUSH);
	}
}
