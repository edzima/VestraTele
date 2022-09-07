<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\CampaignController;
use common\modules\lead\models\LeadCampaign;
use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class CampaignCest {

	/* @see CampaignController::actionIndex() */
	private const ROUTE_INDEX = '/lead/campaign/index';
	/* @see CampaignController::actionCreate() */
	private const ROUTE_CREATE = '/lead/campaign/create';
	/* @see CampaignController::actionView() */
	private const ROUTE_VIEW = '/lead/campaign/view';
	/* @see CampaignController::actionDelete() */
	private const ROUTE_DELETE = '/lead/campaign/delete';

	private const SELECTOR_FORM = '#lead-campaign-form';
	private const SELECTOR_GRID = '#lead-campaign-grid';

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Campaigns');
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeMenuLink('Campaigns');
		$I->click('Campaigns');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Campaigns', 'h1');
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->dontSeeInGridHeader('Owner');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateForm(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->dontSee('Owner', 'label');
	}

	public function checkCreateWithoutOwner(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('Test new name'));
		$I->seeRecord(LeadCampaign::class, [
			'owner_id' => 1,
			'name' => 'Test new name',
		]);
	}

	public function tryCreateWithOwnerAsOtherUser(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('Test new name', 2));
		$I->dontSeeRecord(LeadCampaign::class, [
			'owner_id' => 2,
			'name' => 'Test new name',
		]);
	}

	public function checkViewSelfModel(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$id = $I->haveRecord(LeadCampaign::class, [
			'name' => 'Self campaign',
			'owner_id' => 1,
		]);

		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $id]);
		$I->see('Self campaign', 'h1');
	}

	public function checkDeleteLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$id = $I->haveRecord(LeadCampaign::class, [
			'name' => 'Self campaign',
			'owner_id' => 1,
		]);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeGridDeleteLink(static::SELECTOR_GRID);

		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => $id]), $I->getCSRF());
		$I->seeRecord(LeadCampaign::class, [
			'name' => 'Self campaign',
			'owner_id' => 1,
			'id' => $id,
		]);
		$I->seeResponseCodeIs(405);
	}

	public function checkViewNotSelfModel(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$id = $I->haveRecord(LeadCampaign::class, [
			'name' => 'Not self campaign',
			'owner_id' => 2,
		]);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $id]);
		$I->seeResponseCodeIs(404);
	}

	protected function formParams($name, $owner_id = null, $sort_index = null): array {
		$params = [
			'LeadCampaign[name]' => $name,
			'LeadCampaign[sort_index]' => $sort_index,
		];
		if ($owner_id !== null) {
			$params['LeadCampaign[owner_id]'] = $owner_id;
		}
		return $params;
	}
}
