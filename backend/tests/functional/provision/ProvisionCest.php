<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\ProvisionController;
use backend\tests\Page\provision\ProvisionUpdatePage;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;
use common\models\provision\Provision;
use Yii;

class ProvisionCest {

	/** @see ProvisionController::actionIndex() */
	public const ROUTE_INDEX = '/provision/provision/index';
	/** @see ProvisionController::actionUpdate() */
	public const ROUTE_UPDATE = '/provision/provision/update';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Provisions');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProvisionManager(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Provisions');
		$I->clickMenuLink('Provisions');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInTitle('Provisions', 'h1');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Settlement type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Provision (%)');
		$currencyCode = Yii::$app->formatter->getCurrencySymbol();
		$I->seeInGridHeader("Provision ($currencyCode)");
	}

	public function checkUpdateValue(ProvisionManager $I, ProvisionUpdatePage $page): void {
		$I->amLoggedIn();
		$page->haveFixtures();
		$id = $page->haveProvision(100);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $id]);
		$page->fillValueField(300);
		$I->click('Save');
		$I->seeRecord(Provision::class, ['id' => $id, 'value' => 300]);
	}

	public function checkUpdatePercent(ProvisionManager $I, ProvisionUpdatePage $page): void {
		$I->amLoggedIn();
		$page->haveFixtures();
		$id = $page->haveProvision(300, ['pay_id' => 1]);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $id]);
		$page->fillPercentField(10);
		$I->click('Save');
		$I->seeRecord(Provision::class, ['id' => $id, 'value' => 100]);
	}

}
