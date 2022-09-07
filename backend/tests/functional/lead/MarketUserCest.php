<?php

namespace backend\tests\functional\lead;

use backend\helpers\Url;
use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\controllers\MarketUserController;
use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;

class MarketUserCest {

	/** @see MarketUserController::actionIndex() */
	public const ROUTE_INDEX = '/lead/market-user/index';
	/** @see MarketUserController::actionAccept() */
	public const ROUTE_ACCEPT = '/lead/market-user/accept';
	/** @see MarketUserController::actionAccessRequest() */
	public const ROUTE_ACCESS_REQUEST = '/lead/market-user/access-request';
	/** @see MarketUserController::actionReject() */
	public const ROUTE_REJECT = '/lead/market-user/reject';

	public const PERMISSION = MarketCest::PERMISSION;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::user(),
		);
	}

	public function checkIndexPageWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndexPageWithPermission(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Market User');
	}

	public function actionAccessRequestDefault(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();

		$I->amOnRoute(static::ROUTE_ACCESS_REQUEST, ['market_id' => 1]);

		$I->submitForm('#lead-market-access-request-form', []);

		$I->seeRecord(LeadMarketUser::class, [
			'user_id' => $I->getUser()->getId(),
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS,
			'market_id' => 1,
		]);

		$I->seeEmailIsSent();
	}

	public function actionAccessRequestWithNotDefaultDaysWithoutDetails(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();

		$I->amOnRoute(static::ROUTE_ACCESS_REQUEST, ['market_id' => 1]);

		$I->submitForm('#lead-market-access-request-form', [
			'LeadMarketAccessRequest[days]' => LeadMarketAccessRequest::DEFAULT_DAYS + 1,
		]);

		$I->seeValidationError('Details cannot be blank when Days is other than: ' . LeadMarketAccessRequest::DEFAULT_DAYS . '.');

		$I->dontSeeRecord(LeadMarketUser::class, [
			'user_id' => $I->getUser()->getId(),
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS + 1,
			'market_id' => 1,
		]);

		$I->dontSeeEmailIsSent();
	}

	public function actionAccessRequestWithNotDefaultDaysWithDetails(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();

		$I->amOnRoute(static::ROUTE_ACCESS_REQUEST, ['market_id' => 1]);

		$I->submitForm('#lead-market-access-request-form', [
			'LeadMarketAccessRequest[days]' => LeadMarketAccessRequest::DEFAULT_DAYS + 1,
			'LeadMarketAccessRequest[details]' => 'Please mi Give up for longer term.',

		]);

		$I->seeRecord(LeadMarketUser::class, [
			'user_id' => $I->getUser()->getId(),
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS + 1,
			'details' => 'Please mi Give up for longer term.',
			'market_id' => 1,
		]);

		$I->seeEmailIsSent();
	}

	public function actionAcceptedSelfMarketLead(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$marketId = $I->haveRecord(LeadMarket::class, [
			'creator_id' => $I->getUser()->getId(),
			'lead_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
		]);

		$I->haveRecord(LeadMarketUser::class, [
			'user_id' => 2,
			'market_id' => $marketId,
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS,
		]);

		$I->sendAjaxPostRequest(Url::to([
				static::ROUTE_ACCEPT, 'market_id' => $marketId, 'user_id' => 2,
			]
		), $I->getCSRF()
		);

		$I->seeRecord(LeadMarketUser::class, [
			'user_id' => 2,
			'market_id' => $marketId,
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS,
			'status' => LeadMarketUser::STATUS_ACCEPTED,
		]);

		$I->seeRecord(LeadMarket::class, [
			'id' => $marketId,
			'status' => LeadMarket::STATUS_BOOKED,
		]);
	}

	public function actionRejectSelfMarketLead(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$marketId = $I->haveRecord(LeadMarket::class, [
			'creator_id' => $I->getUser()->getId(),
			'lead_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
		]);

		$I->haveRecord(LeadMarketUser::class, [
			'user_id' => 2,
			'market_id' => $marketId,
			'status' => LeadMarketUser::STATUS_TO_CONFIRM,
			'reserved_at' => '2020-01-01',
		]);

		$I->sendAjaxPostRequest(Url::to([
				static::ROUTE_REJECT, 'market_id' => $marketId, 'user_id' => 2,
			]
		), $I->getCSRF()
		);

		$I->seeRecord(LeadMarketUser::class, [
			'user_id' => 2,
			'market_id' => $marketId,
			'reserved_at' => null,
			'status' => LeadMarketUser::STATUS_REJECTED,
		]);
	}
}
