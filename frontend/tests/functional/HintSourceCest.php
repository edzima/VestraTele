<?php

namespace frontend\tests\functional;

use common\models\user\User;
use frontend\tests\FunctionalTester;

class HintSourceCest {

	private const ROUTE_INDEX = '/hint-city-source/index';

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
		$I->dontSeeMenuLink('Hint sources');
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Hint sources');

		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_HINT);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeMenuLink('Hint sources');
		$I->seeInTitle('Hint Sources');

		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('City');
	}

}
