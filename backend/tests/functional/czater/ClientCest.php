<?php

namespace backend\tests\functional\czater;

use backend\tests\Step\Functional\CzaterManager;
use backend\tests\Step\Functional\Manager;

class ClientCest {

	/** @see ClientController::actionIndex() */
	private const ROUTE_INDEX = '/czater/client/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Clients');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCzaterManager(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Clients');
		$I->clickMenuLink('Clients');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndex(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Clients');
	}
}
