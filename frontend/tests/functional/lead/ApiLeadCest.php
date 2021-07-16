<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\Module;
use frontend\controllers\ApiLeadController;
use frontend\tests\FunctionalTester;

class ApiLeadCest {

	/** @see ApiLeadController::actionLanding() */
	private const ROUTE_LANDING = '/lead/api/landing';

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkLandingForSourceWithoutOwner(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 1,
			'name' => 'Jonny',
			'email' => 'email@example.com',
		]);
		$I->seeRecord(Module::manager()->model, [
			'source_id' => 1,
			'name' => 'Jonny',
			'email' => 'email@example.com',
		]);
		$I->seeEmailIsSent();
	}

	public function checkLandingForSourceWithOwner(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 2,
			'name' => 'Jonny',
			'email' => 'email@example.com',
		]);

		$I->seeRecord(Module::manager()->model, [
			'source_id' => 2,
			'name' => 'Jonny',
			'email' => 'email@example.com',
		]);
		$I->seeEmailIsSent();
	}
}
