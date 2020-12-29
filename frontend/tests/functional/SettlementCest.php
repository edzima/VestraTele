<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePayCalculation;
use frontend\controllers\SettlementController;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\FunctionalTester;

class SettlementCest {

	/** @see SettlementController::actionIndex() */
	public const ROUTE_INDEX = 'settlement/index';

	/** @see SettlementController::actionView() */
	public const ROUTE_VIEW = 'settlement/view';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements()
		);
	}

	public function checkIndexAsIssueUser(IssueUserTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Only for workers.');
	}

	public function checkIndexAsAgent(IssueUserTester $I): void {
		$I->amLoggedInAs($I->grabAgent('with-childs'));
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Yours settlements');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Problem status');
		$I->seeInGridHeader('Issue type');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Value to pay');
		$I->seeInGridHeader('Provider');
		$I->seeInGridHeader('User provision (total)');
		$I->seeInGridHeader('User provision (not pay)');
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$id = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-pay');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $id]);
		$I->seeInLoginUrl();
	}

	public function checkViewAsAgent(FunctionalTester $I): void {
		$I->amLoggedInAs($I->grabAgent('with-childs'));
		$model = $this->grabCalculation($I, 'not-payed');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
	}

	private function grabCalculation(FunctionalTester $I, $index): IssuePayCalculation {
		return $I->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}
}
