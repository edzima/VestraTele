<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\modules\lead\controllers\ReportController;

class LeadReportCest {

	/* @see ReportController::actionIndex() */
	public const ROUTE_INDEX = '/lead/report/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Reports');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Reports');
		$I->clickMenuLink('Reports');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead reports', 'h1');
	}

	public function checkIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Lead Type');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Old Status');
		$I->seeInGridHeader('Answers');
		$I->seeInGridHeader('Details');
	}

	public function checkSchemasLinkInIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Lead Questions');
		$I->click('Lead Questions');
		$I->seeInCurrentUrl(QuestionCest::ROUTE_INDEX);
	}
}
