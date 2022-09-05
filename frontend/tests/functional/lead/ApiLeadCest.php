<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\KeyStorageItem;
use common\modules\lead\Module;
use frontend\controllers\ApiLeadController;
use frontend\tests\FunctionalTester;

class ApiLeadCest {

	/** @see ApiLeadController::actionLanding() */
	private const ROUTE_LANDING = '/lead/api/landing';

	public function _before(FunctionalTester $I): void {
		$I->haveRecord(KeyStorageItem::class, [
			'key' => KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID,
			'value' => 1,
		]);
	}

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
		$I->dontSeeSmsIsSend();
	}

	public function checkLandingForSourceWithPhone(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 1,
			'name' => 'Jonny',
			'phone' => '123123123',
		]);
		$I->seeRecord(Module::manager()->model, [
			'source_id' => 1,
			'name' => 'Jonny',
			'phone' => '+48123123123',
		]);
		$I->seeSmsIsSend();
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
		$I->dontSeeSmsIsSend();
	}
}
