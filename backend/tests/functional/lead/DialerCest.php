<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\Worker;
use common\modules\lead\controllers\DialerController;

class DialerCest {

	/** @see DialerController::actionIndex() */
	public const ROUTE_INDEX = '/lead/dialer/index';
	public const PERMISSION = Worker::PERMISSION_LEAD_DIALER_MANAGER;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		);
	}

	public function checkWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Dialers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Dialers');
		$I->clickMenuSubLink('Dialers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Lead Dialers');
	}

	public function checkLinkOnLeadIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->dontSeeLink('Dialers');
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->seeLink('Dialers');
		$I->click('Dialers', '.lead-index');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndexGrid(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Lead');
		$I->seeInGridHeader('Lead Status');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Dialer Status');
		$I->seeInGridHeader('Type');
		$I->see('Priority');
		$I->see('Created At');
		$I->see('Updated At');
		$I->see('Last At');
	}
}
