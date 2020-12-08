<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\PayController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PayIssueManager;

/**
 * Class PayCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PayCest {

	/**
	 * @see PayController::actionIndex()
	 */
	public const ROUTE_INDEX = '/settlement/pay/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Pays');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsPayIssueManager(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Pays');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Pays');
	}

	public function checkMenuLink(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Pays');
		$I->clickMenuLink('Pays');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Settlement type');
	}

}
