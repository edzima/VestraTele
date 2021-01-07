<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationProblemController;
use backend\tests\Step\Functional\ProblemCalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;

/**
 * Class CalculationProblemStatusCest
 *
 */
class CalculationProblemStatusCest {

	/* @see CalculationProblemController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/calculation-problem/index';

	/* @see CalculationProblemController::actionSet() */
	public const ROUTE_SET = '/settlement/calculation-problem/set';

	public function _before(ProblemCalculationIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkMenuLink(ProblemCalculationIssueManager $I): void {
		$I->seeMenuLink('Uncollectible');
		$I->clickMenuLink('Uncollectible');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkWithProblemsPage(ProblemCalculationIssueManager $I): void {
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Uncollectible settlements');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Problem status');
		$I->seeInGridHeader('Issue type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Value to pay');
		$I->seeInGridHeader('Updated at');
	}

	public function checkSetStatusForNotPayed(ProblemCalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$I->amOnPage([static::ROUTE_SET, 'id' => $model->id]);
		$I->see('Set uncollectible status');
		$I->seeFlash('Setting problem status remove all not payed pays.', 'warning');
	}

	public function checkSetStatusForPayed(ProblemCalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'payed');
		$I->amOnPage([static::ROUTE_SET, 'id' => $model->id]);
		$I->seeFlash('Only not payed calculation can be set problem status.', 'warning');
	}

}
