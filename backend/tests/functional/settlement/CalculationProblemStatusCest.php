<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\modules\settlement\controllers\CalculationProblemController;
use backend\tests\Step\Functional\CalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;

/**
 * Class CalculationProblemStatusCest
 *
 * @see CalculationController::actionProblemStatus()
 */
class CalculationProblemStatusCest {

	/**
	 * @see CalculationProblemController::actionIndex()
	 */
	public const ROUTE_INDEX = '/settlement/calculation-problem/index';

	/**
	 * @see CalculationProblemController::actionSet()
	 */
	public const ROUTE_SET = '/settlement/calculation-problem/set';

	public function _before(CalculationIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkWithProblemsPage(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Settlements with problems');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Problem status');
		$I->seeInGridHeader('Issue type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Value to pay');
		$I->seeInGridHeader('Provider name');
		$I->seeInGridHeader('Created at');
		$I->seeInGridHeader('Updated at');
	}

	public function checkSetStatusForNotPayed(CalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$I->amOnPage([static::ROUTE_SET, 'id' => $model->id]);
		$I->see('Set problem status for calculation: ' . $model->id);
		$I->seeFlash('Setting problem status remove all not payed pays.', 'warning');
	}

	public function checkSetStatusForPayed(CalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'payed');
		$I->amOnPage([static::ROUTE_SET, 'id' => $model->id]);
		$I->seeFlash('Only not payed calculation can be set problem status.', 'warning');
	}

}
