<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\CalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;

/**
 * Class CalculationProblemStatusCest
 *
 * @see CalculationController::actionProblemStatus()
 */
class CalculationProblemStatusCest {

	public const ROUTE = '/settlement/calculation/problem-status';

	public function _before(CalculationIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkSetStatusForNotPayed(CalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Set problem status for calculation: ' . $model->id);
	}

	public function checkSetStatusForPayed(CalculationIssueManager $I): void {
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'payed');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->seeFlash('Only not payed calculation can be set problem status.', 'warning');
	}

}
