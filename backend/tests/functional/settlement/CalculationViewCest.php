<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\user\User;

class CalculationViewCest {

	/** @see CalculationController::actionView() */
	public const ROUTE = '/settlement/calculation/view';

	private SettlementFixtureHelper $settlementFixture;

	public function _before(CreateCalculationIssueManager $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(),
			IssueFixtureHelper::issueUsers(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::type(),
		));
		$I->assignPermission(SettlementFixtureHelper::getTypeManagerPermission());
		$I->amLoggedIn();
	}

	public function checkPayed(CreateCalculationIssueManager $I): void {
		$model = $this->settlementFixture->grabSettlement('payed-with-single-costs');

		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
		$I->dontSeeLink('Generate pays');
		$I->see('Issue');
		$I->seeLink($model->issue->longId);

		$I->dontSeeLink('Create note');
	}

	public function checkNotPayed(CreateCalculationIssueManager $I): void {
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');

		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Settlement ' . $model->getTypeName());
		$I->see('Value to pay');
		$I->dontSeeLink('Generate pays');
	}

	public function checkNotPayedWithPayPermission(CreateCalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_CALCULATION_PAYS);
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');

		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->seeLink('Generate pays');
	}

	public function checkWithNotePermission(CreateCalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_NOTE);
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');

		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->seeLink('Create note');
		$I->click('Create note');
	}

}
