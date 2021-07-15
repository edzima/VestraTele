<?php

namespace backend\tests\functional\czater;

use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\CzaterManager;
use common\modules\czater\controllers\ConsultantController;

class ConsultantCest {

	/**
	 * @see ConsultantController::actionIndex()
	 */
	private const ROUTE_INDEX = '/czater/consultant/index';

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Consultants');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCzaterManager(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Consultants');
		$I->clickMenuLink('Consultants');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndex(CzaterManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Consultants');
	}
}
