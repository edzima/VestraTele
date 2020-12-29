<?php

namespace common\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use frontend\controllers\IssueController;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\_support\PayReceivedTester;
use frontend\tests\functional\SettlementCest;
use frontend\tests\FunctionalTester;

class IssueCest {

	/** @see IssueController::actionIndex() */
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

	public function checkAsAgent(FunctionalTester $I): void {
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amLoggedInAs($I->grabAgent('with-childs')->id);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeMenuLink('Issues');
	}

	public function checkAsIssueUser(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeMenuLink('Issues');
		$I->dontSeeLink('Received pays');
		$I->see('Issues', 'h1');
	}

	public function checkAsPayReceived(PayReceivedTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Received pays');
	}

	public function checkIssueUserLink(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Search issue user');
		$I->click('Search issue user');
		$I->see('Issues users');
	}

	public function checkSettlementLink(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Yours settlements');
		$I->click('Yours settlements');
		$I->seeInCurrentUrl(SettlementCest::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

}
