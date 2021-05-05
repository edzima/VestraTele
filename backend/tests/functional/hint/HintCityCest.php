<?php

namespace backend\tests\functional\hint;

use backend\tests\Step\Functional\HintManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\HintFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\hint\HintCity;

class HintCityCest {

	private const ROUTE_INDEX = '/hint/city/index';
	private const ROUTE_VIEW = '/hint/city/view';
	private const ROUTE_CREATE = '/hint/city/create';
	private const ROUTE_CREATE_DISTRICT = '/hint/city/create-district';
	private const ROUTE_UPDATE = '/hint/city/update';

	private const FORM_SELECTOR = '#hint-city-form';

	public function _fixtures(): array {
		return array_merge(
			TerytFixtureHelper::fixtures(),
			HintFixtureHelper::user(),
			HintFixtureHelper::city()
		);
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Hints');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsHintManager(HintManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Hints');
		$I->clickMenuLink('Hints');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkCreateCityLink(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Create Hint City');
		$I->click('Create Hint City');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCreateDistrictLink(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Create Hint District');
		$I->click('Create Hint District');
		$I->seeInCurrentUrl(static::ROUTE_CREATE_DISTRICT);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexGridView(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('City');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Created At');
		$I->seeInGridHeader('Updated At');
	}

	public function checkCreateSingle(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			1,
			TerytFixtureHelper::SIMC_ID_WEJHEROWO,
			HintCity::STATUS_NEW,
			HintCity::TYPE_CARE_BENEFITS,
		));
		$I->seeRecord(HintCity::class, [
			'city_id' => TerytFixtureHelper::SIMC_ID_WEJHEROWO,
			'user_id' => 1,
			'status' => HintCity::STATUS_NEW,
		]);
	}

	public function checkUpdate(HintManager $I): void {
		$I->amLoggedIn();
		/** @var HintCity $hintCity */
		$hintCity = $I->grabFixture(HintFixtureHelper::CITY, 'new-commission');
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $hintCity->id]);
		$I->seeInTitle('Update Hint City: Bielsko-Biała - Commission refunds');
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			1,
			TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			HintCity::STATUS_DONE,
			HintCity::TYPE_CARE_BENEFITS,
		));
		$I->seeRecord(HintCity::class, [
			'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			'user_id' => 1,
			'status' => HintCity::STATUS_DONE,
		]);
	}

	public function checkViewPage(HintManager $I): void {
		$I->amLoggedIn();
		$hintCity = $I->grabFixture(HintFixtureHelper::CITY, 'new-commission');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $hintCity->id]);
		$I->see('Bielsko-Biała - Commission refunds');
		$I->see('New');
		$I->see('User');
		$I->seeLink('Update');
		$I->seeLink('Delete');
	}

	private function formParams($user_id, $city_id, $status, $type, $details = null) {
		return [
			'HintCityForm[user_id]' => $user_id,
			'HintCityForm[city_id]' => $city_id,
			'HintCityForm[status]' => $status,
			'HintCityForm[type]' => $type,
			'HintCityForm[details]' => $details,

		];
	}
}
