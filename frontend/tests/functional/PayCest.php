<?php

namespace frontend\tests\functional;

use frontend\controllers\PayController;
use frontend\tests\_support\AgentTester;

class PayCest {

	/** @see PayController::actionIndex() */
	public const INDEX = '/pay/index';

	public function checkAsAgent(AgentTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueAgent(AgentTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::INDEX);
		$I->seeResponseCodeIs(403);
	}

}
