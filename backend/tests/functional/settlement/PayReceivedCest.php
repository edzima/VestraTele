<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PayReceivedManager;

class PayReceivedCest {

	const ROUTE_INDEX = '/settlement/pay-received/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Received pays');
	}

	public function checkAsPayReceivedManager(PayReceivedManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Received pays');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInTitle('Received pays');
	}
}
