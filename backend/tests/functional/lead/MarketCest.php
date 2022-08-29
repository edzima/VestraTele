<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\MarketController;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReport;

class MarketCest {

	/** @see MarketController::actionIndex() */
	public const ROUTE_INDEX = '/lead/market/index';
	/** @see MarketController::actionCreate() */
	public const ROUTE_CREATE = '/lead/market/create';

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

	public function checkUsersLink(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Market Users');
		$I->click('Market Users');
		$I->seeInCurrentUrl(MarketUserCest::ROUTE_INDEX);
	}

	public function actionCreate(LeadManager $I): void {
		$I->haveFixtures(
			array_merge(
				LeadFixtureHelper::lead(),
				LeadFixtureHelper::market(),
				LeadFixtureHelper::status(),
				LeadFixtureHelper::reports(),
				LeadFixtureHelper::source(),
			)
		);
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();

		$I->amOnRoute(static::ROUTE_CREATE, ['id' => 2]);

		$I->submitForm('#lead-market-form', [
			'LeadMarketForm[details]' => 'Test Details Create CEST',
		]);

		$I->seeRecord(LeadMarket::class, [
			'details' => 'Test Details Create CEST',
			'creator_id' => $I->getUser()->getId(),
		]);

		$I->seeRecord(LeadReport::class, [
			'details' => 'Move Lead to Market',
			'owner_id' => $I->getUser()->getId(),
			'lead_id' => 2,
		]);
	}
}
