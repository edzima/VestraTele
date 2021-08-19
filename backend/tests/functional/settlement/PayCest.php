<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\PayController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PayIssueManager;
use common\fixtures\helpers\EmailTemplateFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\helpers\Flash;

/**
 * Class PayCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PayCest {

	/* @see PayController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/pay/index';

	/* @see PayController::actionPay() */
	public const ROUTE_PAY = '/settlement/pay/pay';

	private SettlementFixtureHelper $settlementFixture;

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Pays');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsPayIssueManager(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Pays');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Pays');
	}

	public function checkMenuLink(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Pays');
		$I->clickMenuLink('Pays');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(PayIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Settlement type');
		$I->seeInGridHeader('Agent');
		$I->seeInGridHeader('Customer');
	}

	public function checkPay(PayIssueManager $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			EmailTemplateFixtureHelper::fixture(),
		));
		$I->amLoggedIn();
		$pay = $this->settlementFixture->grabPay('not-payed');
		$I->amOnRoute(static::ROUTE_PAY, ['id' => $pay->id]);
		$I->see('Payed pay');
		$I->fillField('Pay at', '2020-01-01');
		$I->click('Save');
		$I->seeFlash('The payment: ' . \Yii::$app->formatter->asCurrency($pay->getValue()) . ' marked as paid.', Flash::TYPE_SUCCESS);
		$I->seeEmailIsSent(2);
	}

}
