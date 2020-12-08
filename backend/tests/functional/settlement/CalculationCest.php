<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\CalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\user\User;

/**
 * Class CalculationIndexCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CalculationCest {

	/** @see CalculationController::actionIndex() */
	public const ROUTE_INDEX = 'settlement/calculation/index';

	/** @see CalculationController::actionToCreate() */
	public const ROUTE_TO_CREATE = 'settlement/calculation/to-create';

	/** @see CalculationController::actionIssue() */
	public const ROUTE_ISSUE = 'settlement/calculation/issue';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Calculations');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCalculationIssueManager(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Settlements');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Settlements');
	}

	public function checkMenuLink(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Settlements');
		$I->clickMenuLink('Settlements');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('To create');
		$I->seeLink('With problems');
		$I->dontSeeLink('Without provisions');
		$I->seeInGridHeader('Issue');
		$I->dontSeeInGridHeader('Problem status');
		$I->seeInGridHeader('Issue type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Issue stage on create');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Value to pay');
		$I->seeInGridHeader('Provider name');
		$I->seeInGridHeader('Created at');
		$I->seeInGridHeader('Updated at');
	}

	public function checkIndexWithProvisionPerrmision(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Without provisions');
		$I->click('Without provisions');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkWithProblemsFromIndexLink(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->click('With problems');
		$I->seeInCurrentUrl(CalculationProblemStatusCest::ROUTE_INDEX);
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
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
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
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Calculations for: ' . $issue->longId);
		$I->seeLink('Create settlement');

		$I->see('To create');
		$I->dontSeeInGridHeader('Issue', '#to-create-grid');
		$I->seeInGridHeader('Type', '#to-create-grid');
		$I->seeInGridHeader('Stage', '#to-create-grid');
		$I->dontSeeInGridHeader('Customer', '#to-create-grid');


		$I->see('Issue calculations');
		$I->dontSeeInGridHeader('Issue', '#calculations-grid');
		$I->seeInGridHeader('Type', '#calculations-grid');
		$I->seeInGridHeader('Issue stage on create');
		$I->seeInGridHeader('Problem status', '#calculations-grid');
		$I->dontSeeInGridHeader('Customer', '#calculations-grid');
		$I->seeInGridHeader('Value with VAT');
	}

	public function checkIssueWithCalculationStageWithoutCalculationOnIssuePage(CalculationIssueManager $I): void {
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
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->click('Create settlement');
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
