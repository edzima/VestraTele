<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;

/**
 * Class CalculationIndexCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CalculationCest {

	public const ROUTE_INDEX = '/settlement/calculation/index';
	public const ROUTE_ISSUE = 'settlement/calculation/issue';
	public const ROUTE_TO_CREATE = 'settlement/calculation/to-create';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Calculations');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCalculationIssueManager(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Calculations');
	}

	public function checkMenuLink(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->clickMenuLink('Calculations');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		//@todo change customer from client
		//	$I->seeInGridHeader('Customer');
		//	$I->seeInGridHeader('Value with VAT');
	}

	public function checkToCreateWithoutMinCountSettings(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_TO_CREATE);
		$I->seeFlash('Min calculation count must be set.', 'warning');
	}

	public function checkToCreateWithMinCountSettings(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amOnPage(static::ROUTE_TO_CREATE);
		$I->dontSeeFlash('Min calculation count must be set.', 'warning');
		$I->see('Issues to create calculations');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Stage');
		$I->seeInGridHeader('Customer');
	}

	public function checkIssueWithoutCalculation(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		/** @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->dontSee('Calculations for: ' . $issue->longId);
		$I->see('Create calculation for: ' . $issue->longId);
	}

	public function checkIssueWithCalculation(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements()
		));
		/** @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Calculations for: ' . $issue->longId);
		$I->seeLink('Create calculation');
		$I->see('To create');
		$I->dontSeeInGridHeader('Issue', '#to-create-grid');
		$I->seeInGridHeader('Type', '#to-create-grid');
		$I->seeInGridHeader('Stage', '#to-create-grid');
		$I->dontSeeInGridHeader('Customer', '#to-create-grid');

		$I->see('Issue calculations');
		$I->dontSeeInGridHeader('Issue', '#calculations-grid');
		$I->seeInGridHeader('Type', '#calculations-grid');
		$I->seeInGridHeader('Problem status', '#calculations-grid');
		$I->dontSeeInGridHeader('Customer', '#calculations-grid');
		$I->seeInGridHeader('Value with VAT');
	}

	public function checkIssueWithCalculationStageWithoutCalculationOnIssuePage(CalculationIssueManager $I) {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		/** @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Create');
		$I->seeInCurrentUrl(CalculationCreateCest::ROUTE);
	}

	public function checkIssueCreateLink(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements()
		));
		/** @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->click('Create calculation');
		$I->seeResponseCodeIsSuccessful();
		$I->seeInCurrentUrl(CalculationCreateCest::ROUTE);
	}

	public function checkNotExistIssue(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => 1000]);
		$I->seeResponseCodeIs(404);
	}

}
