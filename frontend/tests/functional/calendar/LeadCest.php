<?php

namespace frontend\tests\functional\calendar;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\calendar\controllers\LeadController;
use frontend\tests\_support\LeadTester;
use frontend\tests\FunctionalTester;

class LeadCest {

	private const PERMISSION = User::PERMISSION_LEAD;

	/** @see LeadController::actionIndex() */
	private const ROUTE_INDEX = '/calendar/lead/index';
	/** @see LeadController::actionList() */
	private const ROUTE_LIST = '/calendar/lead/list';

	public function _fixtures(): array {
		return
			array_merge(
				LeadFixtureHelper::lead(),
				LeadFixtureHelper::user(),
				LeadFixtureHelper::reminder()
			);
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndexWithPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Calendar - Leads');
		$I->see('New');
		$I->dontSee('Archive');
	}

	public function checkList(LeadTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnPage([static::ROUTE_LIST, 'start' => '2020-01-01', 'end' => '2020-03-01']);
		$I->see('title');
		$I->see('start');
		$I->see('phone');
		$I->see('statusId');
		$I->see('tooltipContent');
		$I->see('2020-01-01');
	}
}
