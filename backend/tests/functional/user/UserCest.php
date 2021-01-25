<?php

namespace backend\tests\functional\user;

use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\WorkersManager;

class UserCest {

	protected const ROUTE_INDEX = '/user/user/index';
	protected const ROUTE_CREATE = '/user/user/create';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsWorkerManager(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
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
		$I->seeInGridHeader('Firstname');
		$I->seeInGridHeader('Lastname');
		$I->seeInGridHeader('Username');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Ip');
		$I->seeInGridHeader('Gender');
		$I->seeInGridHeader('Action at');
	}

	public function checkCreateLink(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Users', 'h1');
		$I->seeLink('Create user');
		$I->click('Create user');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
		$I->see('Create user', 'h1');
	}

}
