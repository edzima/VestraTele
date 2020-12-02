<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;

class CalculationViewCest {

	public const ROUTE = '/settlement/calculation/view';

	public function _before(CalculationIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkPayed(CalculationIssueManager $I): void {
		/** @var IssuePayCalculation $model */
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'payed');

		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
		$I->dontSeeLink('Generate pays');
		$I->see('Issue');
		$I->seeLink($model->issue->longId);
	}

	public function checkNotPayed(CalculationIssueManager $I): void {
		/** @var IssuePayCalculation $model */
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
		$I->see('Value to pay');
		$I->dontSeeLink('Generate pays');
	}
	public function checkNotPayedWithPayPermission(CalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_PAY);
		/** @var IssuePayCalculation $model */
		$model = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->seeLink('Generate pays');
	}


}
