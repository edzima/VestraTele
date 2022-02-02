<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\user\User;
use frontend\controllers\SettlementController;
use frontend\tests\_support\CustomerServiceTester;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\FunctionalTester;

class SettlementCest {

	private SettlementFixtureHelper $settlementFixture;

	/** @see SettlementController::actionIndex() */
	public const ROUTE_INDEX = 'settlement/index';

	/** @see SettlementController::actionView() */
	public const ROUTE_VIEW = 'settlement/view';

	/** @see SettlementController::actionPays() */
	public const ROUTE_PAYS = 'settlement/pays';

	public function _before(FunctionalTester $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::settlement()
		);
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnPage([static::ROUTE_VIEW, 'id' => 1]);
		$I->seeInLoginUrl();
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
		$I->seeInGridHeader('User provision (total)');
		$I->seeInGridHeader('User provision (not pay)');
	}

	public function checkViewAsAgent(FunctionalTester $I): void {
		$I->amLoggedInAs($I->grabAgent('with-childs'));
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
		$I->dontSeeLink('Generate pays');
	}

	public function checkGeneratePaysLinkForNotPayedSettlement(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_CALCULATION_PAYS);
		$I->amLoggedIn();
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->seeLink('Generate pays');
		$I->click('Generate pays');
		$I->seeResponseCodeIsSuccessful();
		$I->seeInCurrentUrl(static::ROUTE_PAYS);
	}

	public function checkGeneratePaysWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE_PAYS, 'id' => $model->id]);
		$I->seeResponseCodeIs(403);
	}

	public function checkGeneratePaysWithPermission(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_CALCULATION_PAYS);
		$I->amLoggedIn();
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE_PAYS, 'id' => $model->id]);
		$I->see('Generate pays for: ' . $model->getTypeName());
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkGeneratePaysLinkForPayedSettlement(IssueUserTester $I): void {
		$I->assignPermission(User::PERMISSION_CALCULATION_PAYS);
		$I->amLoggedIn();
		$model = $this->settlementFixture->grabSettlement('payed-with-single-costs');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->dontSeeLink('Generate pays');
	}

}
