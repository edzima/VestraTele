<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\LeadController;
use common\modules\lead\Module;
use common\tests\helpers\LeadFactory;
use frontend\tests\FunctionalTester;

class LeadCest {

	/**
	 * @see LeadController::actionIndex()
	 */
	private const ROUTE_INDEX = '/lead/lead/index';

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

	public function checkWithPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkAsUserWithoutLeads(FunctionalTester $I): void {
		$I->amLoggedInAs(3);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('No results found.', '#leads-grid');
	}

	public function checkAsUserWithLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-1 of 1 item.', '#leads-grid');
		Module::manager()->pushLead(LeadFactory::createLead([
			'owner_id' => 1,
			'phone' => '505-505-505',
			'source_id' => 1,
			'status_id' => 1,
		]));
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-2 of 2 items.', '#leads-grid');
	}

}
