<?php

namespace backend\tests\functional\hint;

use backend\tests\Step\Functional\HintManager;
use backend\tests\Step\Functional\Manager;

class HintCitySourceCest {

	private const ROUTE_INDEX = '/hint/city-source/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Hints');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsHintManager(HintManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Hints');
		$I->seeMenuSubLink('Hint Cities Sources');
		$I->clickMenuSubLink('Hint Cities Sources');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndexGridView(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('City');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Rating');
		$I->seeInGridHeader('Details');
	}

}
