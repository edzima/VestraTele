<?php

namespace common\tests\functional;

use frontend\tests\_support\IssueUserTester;
use frontend\tests\FunctionalTester;

class IssueCest {

	public const ROUTE_INDEX = '/issue/index';

	public function checkWithoutLogin(FunctionalTester $I): void {
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Issues');
	}

	public function checkAsUser(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Issues');
	}

	public function checkAsIssueUser(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeMenuLink('Issues');
		$I->see('Issues', 'h1');
	}

	public function checkIssueUserLink(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Search issue user');
		$I->click('Search issue user');
		$I->see('Issues users');
	}
}
