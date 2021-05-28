<?php

namespace backend\tests\functional\lead;

use backend\modules\user\controllers\UserController;
use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;

class UserCest {

	/* @see UserController::actionIndex() */
	public const ROUTE_INDEX = '/lead/user/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Users');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Users');
		$I->clickMenuLink('Users');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Users', 'h1');
	}

	public function checkIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Lead');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Type');
	}

}
