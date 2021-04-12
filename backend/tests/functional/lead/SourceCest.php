<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadSource;

class SourceCest {

	private const ROUTE_INDEX = '/lead/source/index';
	private const ROUTE_CREATE = '/lead/source/create';

	protected const FORM_SELECTOR = '#lead-source-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Source');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Sources');
		$I->clickMenuLink('Sources');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Sources', 'h1');
	}

	public function checkIndexGrid(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('URL');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateWithoutURL(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->haveFixtures(LeadFixtureHelper::source());
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some name',
		));
		$I->seeRecord(LeadSource::class, [
			'name' => 'Some name',
		]);
	}

	public function checkCreateWithoutUser(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->haveFixtures(LeadFixtureHelper::source());
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some name',
			'http://google.com',
			null
		));
		$I->seeRecord(LeadSource::class, [
			'name' => 'Some name',
			'url' => 'http://google.com',
			'owner_id' => null,
		]);
	}

	public function checkWithUser(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->haveFixtures(LeadFixtureHelper::source());
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some name',
			'http://google.com',
			1
		));
		$I->seeRecord(LeadSource::class, [
			'name' => 'Some name',
			'url' => 'http://google.com',
			'owner_id' => 1,
		]);
	}

	protected function formParams($name, $url = null, $owner_id = null, $sort_index = null): array {
		return [
			'LeadSource[name]' => $name,
			'LeadSource[url]' => $url,
			'LeadSource[owner_id]' => $owner_id,
			'LeadSource[sort_index]' => $sort_index,
		];
	}

}
