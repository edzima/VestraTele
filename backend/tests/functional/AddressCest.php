<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\Manager;

class AddressCest {

	public const ROUTE_INDEX = '/address/index';

	public function checkIndex(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeMenuLink('Addresses');
		$I->see('Addresses', 'h1');
		$I->seeInGridHeader('Postal code');
		$I->seeInGridHeader('City');
		$I->seeInGridHeader('Info');
	}
}
