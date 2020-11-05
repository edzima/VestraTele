<?php

namespace backend\tests\functional\user;

use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;

class UserCest {

	protected const ROUTE_INDEX = '/user/user/index';
	
	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSee('Users', 'h1');
	}

	public function checkAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Users', 'h1');
	}

	public function checkIndex(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Users', 'h1');
		$I->seeLink('Create user');
		$I->click('Create user');
		$I->see('Create user', 'h1');
	}

}
