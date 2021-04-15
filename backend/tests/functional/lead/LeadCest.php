<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\controllers\LeadController;

class LeadCest {

	/** @see LeadController::actionIndex() */
	private const ROUTE_INDEX = '/lead/lead/index';
	/** @see LeadController::actionCreate() */
	private const ROUTE_CREATE = '/lead/lead/create';

	private const FORM_SELECTOR = '#lead-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Leads');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Leads');
		$I->clickMenuLink('Leads');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Leads', 'h1');
	}

	public function checkCreateLink(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Create Lead');
		$I->click('Create Lead');
		$I->see('Create Lead', 'h1');
	}

	public function checkIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Email');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Reports');
	}

	public function checkCreate(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->click('Save');
	}

}
