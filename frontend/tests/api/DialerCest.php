<?php

namespace frontend\tests\api;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\Module;
use common\tests\helpers\LeadFactory;
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
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::user(),
		);
	}

	public function _before(): void {
		Lead::deleteAll();
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

	public function checkCallWithoutLeads(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(404);
	}

	public function checkCallWithNewLead(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$lead = $this->pushLead('123 123 123');
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseIsJson();

		$I->see($lead->getId());
		$I->see('123123123');

		$I->seeResponseJsonMatchesJsonPath('$.id');
		$I->seeResponseJsonMatchesJsonPath('$.phone');
		$I->dontSeeResponseJsonMatchesJsonPath('$.name');
		$I->dontSeeResponseJsonMatchesJsonPath('$.email');
		$I->seeResponseMatchesJsonType([
			'id' => 'integer',
			'phone' => 'string',
		]);

		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(404);
		$I->sendPost(Url::to([static::ROUTE_ANSWERED, 'id' => $lead->getId()]));
		$I->seeResponseCodeIsSuccessful();
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(404);
	}

	public function checkCallWithNewLeadWithOwner(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$this->pushLead('123-123-123', ['owner_id' => 1]);
		$I->sendPost(static::ROUTE_CALLING);
		$I->seeResponseCodeIs(404);
	}

	public function checkAnswered(ApiTester $I): void {
		$I->amHeaderAuth(static::ACCESS_TOKEN, static::HEADER_AUTH);
		$I->sendPost(Url::to([static::ROUTE_ANSWERED, 'id' => 1]));
	}

	private function pushLead(string $phone, array $config = []): ActiveLead {
		$config['phone'] = $phone;
		if (!isset($config['status_id'])) {
			$config['status_id'] = LeadStatusInterface::STATUS_NEW;
		}
		if (!isset($config['name'])) {
			$config['name'] = __METHOD__;
		}
		if (!isset($config['source_id'])) {
			$config['source_id'] = 1;
		}
		return Module::manager()->pushLead(LeadFactory::createLead($config));
	}

}
