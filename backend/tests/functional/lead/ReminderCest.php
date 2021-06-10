<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;

class ReminderCest {

	private const ROUTE_INDEX = '/lead/reminder/reminder/index';

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}
}
