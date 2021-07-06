<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\modules\lead\controllers\ReminderController;

class ReminderCest {

	/* @see ReminderController::actionIndex() */
	private const ROUTE_INDEX = '/lead/reminder/index';
	/* @see ReminderController::actionCreate() */
	public const ROUTE_REPORT = '/lead/reminder/create';

	public function checkMenuLink(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Reminders');
		$I->clickMenuLink('Reminders');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Lead Reminders', 'title');
		$I->seeCheckboxIsChecked('Only Delayed');
		$I->seeInGridHeader('Priority');
		$I->seeInGridHeader('Created At');
		$I->seeInGridHeader('Date At');
		$I->seeInGridHeader('Details');
	}
}
