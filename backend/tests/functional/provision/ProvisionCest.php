<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\ProvisionController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use Yii;

class ProvisionCest {

	/** @see ProvisionController::actionIndex() */
	public const ROUTE_INDEX = '/provision/provision/index';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
			ProvisionFixtureHelper::provision()
		);
	}

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

}
