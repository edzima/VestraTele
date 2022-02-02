<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\Worker;
use common\modules\lead\controllers\SmsController;
use frontend\tests\FunctionalTester;

class SmsCest {

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	/** @see SmsController::actionPush() */
	public const ROUTE_PUSH = '/lead/sms/push';

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeInLoginUrl();
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithSmsPermissionWithoutLeadPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithSmsPermissionAndLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->assignPermission(Worker::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->see('Send SMS to Lead:');
		$I->fillField('Message', 'Test Message');
		$I->click('Send');
		$I->seeJobIsPushed();
	}
}
