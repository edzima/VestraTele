<?php

namespace frontend\tests\api;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\entities\Dialer;
use common\modules\lead\entities\LeadDialerEntity;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadReport;
use frontend\controllers\LeadDialerController;
use frontend\helpers\Url;
use frontend\tests\ApiTester;

class DialerCest {

	protected const HEADER_AUTH = 'Dialer-Api-Key';
	protected const ACCESS_TOKEN = 'dasdar1r21dsafvsdg2';

	/* @see LeadDialerController::actionCall() */
	private const ROUTE_CALLING = '/lead/dialer/call';
	/* @see LeadDialerController::actionAnswered() */
	protected const ROUTE_ANSWERED = '/lead/dialer/answered';

	/* @see LeadDialerController::actionNotAnswered() */
	protected const ROUTE_NOT_ANSWERED = '/lead/dialer/not-answered';

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::dialer()
		);
	}

	public function checkCallWithoutAuth(ApiTester $I): void {
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(401);
	}

	public function checkCallAsGet(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendGet(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(405);
	}

	public function checkCallWithoutDialers(ApiTester $I): void {
		LeadDialer::deleteAll();
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(404);
	}

	public function checkCallAndEstablish(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIsSuccessful();
		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_CALLING,
		]);

		/**
		 * @var LeadDialer $leadDialer
		 */
		$leadDialer = $I->grabRecord(LeadDialer::class, ['id' => 2]);

		$I->seeRecord(LeadReport::class, [
			'lead_id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_CALLING,
			'old_status_id' => $leadDialer->lead->getStatusId(),
		]);

		$I->dontSeeRecord(Lead::class, [
			'id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_CALLING,
		]);

		$I->sendPost(static::ROUTE_CALLING);
		$I->seePageNotFound();

		$I->sendPost(Url::to([static::ROUTE_ANSWERED, 'id' => 2]));
		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_ESTABLISH,
		]);
	}

	public function checkCallAndNotEstablish(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIsSuccessful();
		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_CALLING,
		]);

		/**
		 * @var LeadDialer $leadDialer
		 */
		$leadDialer = $I->grabRecord(LeadDialer::class, ['id' => 2]);

		$I->seeRecord(LeadReport::class, [
			'lead_id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_CALLING,
			'old_status_id' => $leadDialer->lead->getStatusId(),
		]);

		$I->dontSeeRecord(Lead::class, [
			'id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_CALLING,
		]);

		$I->sendPost(static::ROUTE_CALLING);
		$I->seePageNotFound();

		$I->sendPost(Url::to([static::ROUTE_NOT_ANSWERED, 'id' => 2]));
		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_NOT_ESTABLISH,
		]);

		$I->sendPost(static::ROUTE_CALLING);

		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => LeadDialerEntity::STATUS_NEXT_CALL_INTERVAL_NOT_EXCEEDED,
		]);

		$I->seePageNotFound();
	}

	public function checkEstablishNotEstablished(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->dontSeeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_ESTABLISH,
		]);

		$I->sendPost(Url::to([static::ROUTE_ANSWERED, 'id' => 2]));
		$I->seeRecord(LeadDialer::class, [
			'id' => 2,
			'status' => Dialer::STATUS_ESTABLISH,
		]);

		$I->seeResponseContainsJson(['success' => true]);

		/**
		 * @var LeadDialer $leadDialer
		 */
		$leadDialer = $I->grabRecord(LeadDialer::class, ['id' => 2]);
		$I->seeRecord(LeadReport::class, [
			'lead_id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_ESTABLISH,
			'old_status_id' => $leadDialer->lead->status_id,
		]);
	}

	public function checkEstablishAlreadyEstablish(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(Url::to([static::ROUTE_ANSWERED, 'id' => 1]));
		$I->seeRecord(LeadDialer::class, [
			'id' => 1,
			'status' => Dialer::STATUS_ESTABLISH,
		]);

		$I->seeResponseContainsJson(['success' => false]);

		/**
		 * @var LeadDialer $leadDialer
		 */
		$leadDialer = $I->grabRecord(LeadDialer::class, ['id' => 1]);
		$I->dontSeeRecord(LeadReport::class, [
			'lead_id' => $leadDialer->lead_id,
			'status_id' => Dialer::STATUS_ESTABLISH,
		]);
	}

}
