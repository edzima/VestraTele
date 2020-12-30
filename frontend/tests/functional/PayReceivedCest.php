<?php

namespace frontend\tests\functional;

use frontend\controllers\PayReceivedController;
use frontend\tests\_support\AgentTester;
use frontend\tests\_support\PayReceivedTester;

class PayReceivedCest {

	/** @see PayReceivedController::actionIndex() */
	public const INDEX = '/pay-received/index';

	public function checkAsAgent(AgentTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsPayReceived(PayReceivedTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::INDEX);
		$I->see('Received pays', 'h1');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Date at');
	}

}
