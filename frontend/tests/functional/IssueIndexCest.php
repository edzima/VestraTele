<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;
use frontend\controllers\IssueController;
use frontend\tests\_support\CustomerServiceTester;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\_support\PayReceivedTester;
use frontend\tests\FunctionalTester;

class IssueIndexCest {

	/** @see IssueController::actionIndex() */
	public const ROUTE_INDEX = '/issue/index';

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Issues');
	}

	public function checkAsUser(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Issues');
	}

	public function checkGrid(IssueUserTester $I): void {
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Entity');
		$I->seeInGridHeader('Stage');
		$I->seeInGridHeader('Agent');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Phone');
		$I->see('Updated At');
		$I->see('Notes');
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
		$I->dontSeeLink('Create note');
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
