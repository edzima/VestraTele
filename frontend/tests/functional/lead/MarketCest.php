<?php

namespace frontend\tests\functional\lead;

use common\models\user\User;
use common\modules\lead\controllers\MarketController;
use frontend\tests\_support\LeadTester;

class MarketCest {

	private const PERMISSION = User::PERMISSION_LEAD_MARKET;
	/** @see MarketController::actionUser() */
	private const ROUTE_USER = '/lead/market/user';

	public function checkNotVisibleMarketLinkOnLeadIndexPageWithoutPermission(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->dontSeeLink('Lead Markets');
	}

	public function checkMarketLinkOnLeadIndexPageWithPermission(LeadTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->seeLink('Lead Markets');
		$I->click('Lead Markets');
		$I->amOnPage(static::ROUTE_USER);
	}
}
