<?php

namespace backend\tests\functional\user;

use backend\modules\user\controllers\TraitController;
use backend\tests\Step\Functional\Manager;

class TraitCest {

	/** @see TraitController::actionIndex() */
	public const ROUTE_INDEX = '/user/trait/index';

	public function indexPageAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkCustomerIndexPageNotVisibleTraitsButtonForManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(CustomerIndexCest::ROUTE_INDEX);
		$I->dontSeeLink('Traits');
	}
}
