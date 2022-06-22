<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\models\user\User;
use common\modules\lead\controllers\MarketController;

class MarketCest {

	/** @see MarketController::actionIndex() */
	public const ROUTE_INDEX = '/lead/market/index';
	public const PERMISSION = User::PERMISSION_LEAD_MARKET;

	public function checkMenuLinkWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Leads Market');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLinkWithPermission(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Leads Market');
		$I->clickMenuSubLink('Leads Market');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}
}
