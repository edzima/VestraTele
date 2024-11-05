<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\PayController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PayIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\helpers\Flash;
use Yii;

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

	public function checkPayHonorarium(PayIssueManager $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::type(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED),
		));
		$I->amLoggedIn();
		$id = $this->settlementFixture->havePay(100, [
			'settlement' => [
				'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
			],
		]);
		$I->amOnRoute(static::ROUTE_PAY, ['id' => $id]);
		$I->see('Payed pay');
		$I->fillField('Pay at', '2020-01-01');
		$I->click('Save');
		$I->seeFlash('The payment: ' . Yii::$app->formatter->asCurrency(100) . ' marked as paid.', Flash::TYPE_SUCCESS);
		$I->seeEmailIsSent(2);
		$I->wantTo('See push SMS to Agent, not to Customer, default is disabled for them.');
		$I->seeJobIsPushed(1);
	}

	public function checkPayAdministrative(PayIssueManager $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::type(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::pay(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED),
		));
		$I->amLoggedIn();
		$id = $this->settlementFixture->havePay(100, [
			'settlement' => [
				'type_id' => SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
			],
		]);
		$I->amOnRoute(static::ROUTE_PAY, ['id' => $id]);
		$I->see('Payed pay');
		$I->fillField('Pay at', '2020-01-01');
		$I->click('Save');
		$I->seeFlash('The payment: ' . Yii::$app->formatter->asCurrency(100) . ' marked as paid.', Flash::TYPE_SUCCESS);
		$I->dontSeeEmailIsSent();
		$I->dontSeeJobIsPushed();
	}

}
