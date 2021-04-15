<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadCampaign;

class CampaignCest {

	private const ROUTE_INDEX = '/lead/campaign/index';
	private const ROUTE_CREATE = '/lead/campaign/create';

	protected const FORM_SELECTOR = '#lead-campaign-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Campaigns');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Campaigns');
		$I->clickMenuLink('Campaigns');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Campaigns', 'h1');
	}

	public function checkIndexGrid(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateWithoutUser(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->haveFixtures(LeadFixtureHelper::campaign());
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some name',
			null
		));
		$I->seeRecord(LeadCampaign::class, [
			'name' => 'Some name',
			'owner_id' => null,
		]);
	}

	public function checkWithUser(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->haveFixtures(LeadFixtureHelper::campaign());
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some name',
			1
		));
		$I->seeRecord(LeadCampaign::class, [
			'name' => 'Some name',
			'owner_id' => 1,
		]);
	}

	protected function formParams($name, $owner_id = null, $sort_index = null): array {
		return [
			'LeadCampaign[name]' => $name,
			'LeadCampaign[owner_id]' => $owner_id,
			'LeadCampaign[sort_index]' => $sort_index,
		];
	}

}
