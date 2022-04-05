<?php

namespace backend\tests\functional\czater;

use backend\tests\Step\Functional\CzaterManager;
use backend\tests\Step\Functional\Manager;
use common\modules\czater\controllers\ConvController;

class ConvCest {

	/** @see ConvController::actionIndex() */
	private const ROUTE_INDEX = '/czater/conv/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Convs');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCzaterManager(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Convs');
		$I->clickMenuLink('Convs');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndex(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Convs', 'h1');
	}
}
