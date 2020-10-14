<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;

class WorkerCest {

	protected const ROUTE_INDEX = '/user/worker/index';
	protected const ROUTE_CREATE = '/user/worker/create';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSee('Workers', 'h1');
	}

	public function checkCreateAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->dontSee('Create worker', 'h1');
	}

	public function checkAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Workers', 'h1');
		$I->seeLink('Create worker');
		$I->click('Create worker');
		$I->see('Create worker', 'h1');
	}

}
