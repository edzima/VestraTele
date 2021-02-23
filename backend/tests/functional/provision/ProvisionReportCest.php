<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\ReportController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;

class ProvisionReportCest {

	/** @see ReportController::actionIndex() */
	public const ROUTE_INDEX = '/provision/report/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Reports');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProvisionManager(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Reports');
		$I->clickMenuLink('Reports');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Reports', 'h1');
		$I->seeInGridHeader('User');
	}
}
