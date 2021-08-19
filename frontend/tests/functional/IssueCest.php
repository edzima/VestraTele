<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\user\User;
use frontend\controllers\IssueController;
use frontend\tests\_support\CustomerServiceTester;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\_support\PayReceivedTester;
use frontend\tests\FunctionalTester;

class IssueCest {

	private IssueFixtureHelper $issueFixture;

	/** @see IssueController::actionIndex() */
	public const ROUTE_INDEX = '/issue/index';
	/** @see IssueController::actionView() */
	public const ROUTE_VIEW = '/issue/view';

	public function _before(FunctionalTester $I): void {
		$this->issueFixture = new IssueFixtureHelper($I);
	}

	public function checkAsGuest(FunctionalTester $I): void {
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

	public function checkView(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->id]);
		$I->seeResponseCodeIsClientError();
	}

	public function checkViewAsCustomerService(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->id]);
		$I->see($issue->longId, 'h1');
		$I->dontSeeLink('Create note');
	}

	public function checkSummonGrid(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::types(),
				IssueFixtureHelper::users(),
				IssueFixtureHelper::entityResponsible(),
				IssueFixtureHelper::summon()
			)
		);
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->id]);
		$I->see('Summons');
		$I->dontSeeInGridHeader('Issue', '#summon-grid');
		$I->dontSeeInGridHeader('Customer', '#summon-grid');
		$I->seeInGridHeader('Type', '#summon-grid');
		$I->seeInGridHeader('Status', '#summon-grid');
		$I->seeInGridHeader('Title', '#summon-grid');
		$I->dontSeeInGridHeader('Owner', '#summon-grid');
		$I->seeInGridHeader('Contractor', '#summon-grid');
	}

	public function checkNoteLinkWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_VIEW);
		$I->dontSeeLink('Create note');
	}

	public function checkNoteLinkWithPermission(CustomerServiceTester $I): void {
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::types(),
				IssueFixtureHelper::note(),
			)
		);

		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->id]);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeInCurrentUrl(NoteCest::ROUTE_ISSUE);
	}

}
