<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\LeadController;
use common\modules\lead\Module;
use common\tests\helpers\LeadFactory;
use frontend\tests\_support\LeadTester;
use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class LeadCest {

	/* @see LeadController::actionIndex() */
	private const ROUTE_INDEX = '/lead/lead/index';
	/* @see LeadController::actionCreate() */
	private const ROUTE_CREATE = '/lead/lead/create';
	/* @see LeadController::actionView() */
	private const ROUTE_VIEW = '/lead/lead/view';
	/* @see LeadController::actionDelete() */
	private const ROUTE_DELETE = '/lead/lead/delete';

	private const SELECTOR_LEAD_GRID = '#leads-grid';

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
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Source');
		$I->seeInGridHeader('Campaign');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Reports');
	}

	public function checkAsUserWithoutLeads(FunctionalTester $I): void {
		$I->amLoggedInAs(3);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('No results found.', static::SELECTOR_LEAD_GRID);
	}

	public function checkAsUserWithLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-1 of 1 item.', static::SELECTOR_LEAD_GRID);
		Module::manager()->pushLead(LeadFactory::createLead([
			'owner_id' => 1,
			'phone' => '505-505-505',
			'source_id' => 1,
			'status_id' => 1,
		]));
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-2 of 2 items.', static::SELECTOR_LEAD_GRID);
	}

	public function trySearchNotSelfLeads(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX, [
			'LeadSearch[user_id]' => 2,
		]);
	}

	public function checkCreateEmpty(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->click('Save');
		$I->seeValidationError('Phone cannot be blank when email is blank.');
		$I->seeValidationError('Email cannot be blank when phone is blank.');
	}

	public function checkCreateOnlyPhone(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->fillField('Phone', '789-789-789');
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkViewPageSelfLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 1]);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkViewPageNotSelfLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 2]);
		$I->seeResponseCodeIs(404);
	}

	public function checkDeleteLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeGridDeleteLink(static::SELECTOR_LEAD_GRID);
		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => 2]), $I->getCSRF());
		$I->seeResponseCodeIs(405);
	}

}
