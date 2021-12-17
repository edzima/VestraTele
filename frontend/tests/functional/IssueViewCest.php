<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\user\User;
use common\models\user\Worker;
use frontend\tests\_support\CustomerServiceTester;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\FunctionalTester;

class IssueViewCest {

	/** @see IssueController::actionView() */
	public const ROUTE_VIEW = '/issue/view';

	private IssueFixtureHelper $issueFixture;

	public function _before(FunctionalTester $I): void {
		$this->issueFixture = new IssueFixtureHelper($I);
	}

	protected function grabIssue($index = 0): IssueInterface {
		return $this->issueFixture->grabIssue($index);
	}

	public function checkViewNotSelfIssue(IssueUserTester $I): void {
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amLoggedInAs(UserFixtureHelper::TELE_2);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $this->grabIssue()->getIssueId()]);
		$I->seeResponseCodeIs(404);
	}

	public function checkViewAsCustomerService(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$issue = $this->grabIssue();
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
		$I->see($issue->getIssueName(), 'h1');
		$I->dontSeeLink('Create note');
		$I->dontSeeLink('Create Summon');
		$I->dontSeeLink('Change Stage');
		$I->dontSeeLink('Send SMS');
	}

	public function checkViewArchivedAsCustomerServiceWithoutArchivePermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$issue = $this->grabIssue('archived');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
		$I->seeResponseCodeIs(403);
		$I->see('The Issue is archived. You are not authorized to view the archive.');
	}

	public function checkViewArchivedAsCustomerServiceWithArchivePermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_ARCHIVE);
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$issue = $this->grabIssue('archived');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
		$I->see($issue->getIssueName(), 'h1');
	}

	public function checkSummonGrid(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::stageAndTypesFixtures(),
				IssueFixtureHelper::users(),
				IssueFixtureHelper::entityResponsible(),
				IssueFixtureHelper::summon()
			)
		);
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
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
				IssueFixtureHelper::stageAndTypesFixtures(),
				IssueFixtureHelper::note(),
			)
		);

		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		$issue = $this->grabIssue();
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeInCurrentUrl(NoteCest::ROUTE_ISSUE);
	}

	public function checkStageChangeLinkWithPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->assignPermission(Worker::PERMISSION_ISSUE_STAGE_CHANGE);
		$issue = $this->grabIssue();
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $issue->getIssueId()]);
		$I->seeLink('Change Stage');
		$I->click('Change Stage');
		$I->seeInCurrentUrl(IssueStageCest::ROUTE);
	}
}
