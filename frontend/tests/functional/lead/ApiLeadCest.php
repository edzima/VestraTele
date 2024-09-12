<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\KeyStorageItem;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\Module;
use frontend\controllers\ApiLeadController;
use frontend\tests\FunctionalTester;

class ApiLeadCest {

	/** @see ApiLeadController::actionLanding() */
	private const ROUTE_LANDING = '/lead/api/landing';
	/** @see ApiLeadController::actionCustomer() */
	private const ROUTE_CUSTOMER = '/lead/api/customer';
	/** @see ApiLeadController::actionZapier() */
	private const ROUTE_ZAPIER = '/lead/api/zapier';

	public function _before(FunctionalTester $I): void {
		$I->haveRecord(KeyStorageItem::class, [
			'key' => KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID,
			'value' => 1,
		]);
	}

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::campaign(),
			LeadFixtureHelper::leads(),
		);
	}

	public function checkCustomerAction(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_CUSTOMER, [
			'source_id' => 1,
			'name' => 'Jonny',
			'email' => 'email@example.com',
			'date_at' => '2020-01-01 12:00:00',
		]);
		$I->seeRecord(Module::manager()->model, [
			'source_id' => 1,
			'name' => 'Jonny',
			'email' => 'email@example.com',
			'provider' => Lead::PROVIDER_CRM_CUSTOMER,
		]);
		$I->seeEmailIsSent();
		$I->dontSeeSmsIsSend();
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
			'provider' => Lead::PROVIDER_FORM_LANDING,
		]);
		$I->seeEmailIsSent();
		$I->dontSeeSmsIsSend();
	}

	public function checkLandingForSourceWithPhoneAndSmsTemplate(FunctionalTester $I): void {
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

	public function checkLandingForSourceWithPhoneAndWithoutSmsTemplate(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 3,
			'name' => 'Dep',
			'phone' => '123123123',
		]);
		$I->seeRecord(Module::manager()->model, [
			'source_id' => 3,
			'name' => 'Dep',
			'phone' => '+48123123123',
		]);
		$I->dontSeeSmsIsSend();
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

	public function checkZapierWithPixelCampaign(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_ZAPIER, [
			'source_id' => 2,
			'name' => 'Jonny',
			'email' => 'email@example.com',
			'fb_ad_id' => '120210349625950400',
			'fb_ad_name' => 'FilmNowyNkz – kopia',
			'fb_adset_id' => '120210349625920400',
			'fb_adset_name' => 'Sankcje wszyscy 30+',
			'fb_campaign_id' => '120210349625930400',
			'fb_campaign_name' => 'BO_Sankcje_LeadAds_10052024',
		]);

		$I->seeRecord(LeadCampaign::class, [
			'type' => LeadCampaign::TYPE_AD,
			'entity_id' => '120210349625950400',
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
