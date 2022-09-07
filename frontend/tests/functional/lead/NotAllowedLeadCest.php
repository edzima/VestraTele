<?php

namespace frontend\tests\functional\lead;

use Codeception\Example;
use frontend\tests\_support\LeadTester;
use frontend\tests\FunctionalTester;

abstract class NotAllowedLeadCest {

	abstract protected function routes(): array;

	/**
	 * @dataProvider routes
	 * @param FunctionalTester $I
	 * @param Example $example
	 */
	public function checkAsGuest(FunctionalTester $I, Example $example): void {
		$I->amOnRoute($example[0]);
		$I->seeInLoginUrl();
	}

	/**
	 * @dataProvider routes
	 * @param LeadTester $I
	 * @param Example $example
	 */
	public function checkWithLeadPermission(LeadTester $I, Example $example): void {
		$I->amLoggedIn();
		$I->amOnRoute($example[0]);
		$I->seeResponseCodeIs(403);
	}
}
