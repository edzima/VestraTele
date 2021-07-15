<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use frontend\tests\_support\LeadTester;
use frontend\tests\FunctionalTester;

class ReminderCest {

	private const ROUTE_INDEX = '/lead/reminder/index';
	private const ROUTE_CREATE = '/lead/reminder/create';

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reminder()
		);
	}

	public function actionIndex(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Lead Reminders');
	}

	public function actionCreateForNotSelfLead(LeadTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnPage([static::ROUTE_CREATE, 'id' => 2]);
		$I->seeResponseCodeIs(404);
	}

	public function actionCreate(LeadTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnPage([static::ROUTE_CREATE, 'id' => 1]);
		$I->see('Create Reminder for Lead: #1');
		$I->click('Save');
		$I->seeInCurrentUrl(LeadCest::ROUTE_VIEW);
	}
}
