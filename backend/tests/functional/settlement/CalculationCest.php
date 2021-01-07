<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\Bookkeeper;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProblemCalculationIssueManager;
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
		$I->dontSeeMenuLink('Settlements');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCreateCalculationIssueManager(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculation to create');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProblemsCalculationIssueManager(ProblemCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Calculation to create');
		$I->seeMenuLink('Uncollectible');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsBookkeeper(Bookkeeper $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Settlements');
		$I->seeMenuLink('Calculation to create');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Settlements', 'h1');
	}

	public function checkIndex(Bookkeeper $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('To create');
		$I->seeLink('Uncollectible');
		$I->dontSeeLink('Without provisions');
		$I->seeInGridHeader('Issue');
		$I->dontSeeInGridHeader('Problem status');
		$I->seeInGridHeader('Issue type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Value to pay');
		$I->seeInGridHeader('Updated at');
	}

	public function checkWithProvisionPermission(Bookkeeper $I): void {
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Without provisions');
		$I->click('Without provisions');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkWithProblemsFromIndexLink(ProblemCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->click('Uncollectible');
		$I->seeInCurrentUrl(CalculationProblemStatusCest::ROUTE_INDEX);
	}

	public function checkToCreateWithoutMinCountSettings(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_TO_CREATE);
		$I->seeFlash('Min calculation count must be set.', 'warning');
	}

	public function checkToCreateWithMinCountSettings(CreateCalculationIssueManager $I): void {
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

	public function checkIssueWithoutCalculation(Bookkeeper $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		/** @var Issue $issue */
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Calculations for: ' . $issue->longId);
	}

	public function checkIssueWithCalculationAndNotMinLimit(Bookkeeper $I): void {
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

		$I->see('Issue calculations');
		$I->seeInGridHeader('Type', '#calculation-grid');
		$I->seeInGridHeader('Problem status', '#calculation-grid');
		$I->dontSeeInGridHeader('Customer', '#calculation-grid');
		$I->seeInGridHeader('Value with VAT');
	}


	public function checkIssueCreateLink(Bookkeeper $I): void {
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

	public function checkNotExistIssue(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => 1000]);
		$I->seeResponseCodeIs(403);
	}

}
