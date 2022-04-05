<?php

namespace backend\tests\functional\czater;

use backend\tests\Step\Functional\CzaterManager;
use backend\tests\Step\Functional\Manager;
use common\modules\czater\controllers\CallController;

class CallCest {

	/** @see CallController::actionIndex() */
	private const ROUTE_INDEX = '/czater/call/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Calls');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCzaterManager(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calls');
		$I->clickMenuLink('Calls');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndex(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Calls', 'h1');
	}
}
