<?php

namespace backend\tests\functional\lead;

use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\controllers\ReportController;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;

class ReportCest {

	/* @see ReportController::actionIndex() */
	public const ROUTE_INDEX = '/lead/report/index';
	/* @see ReportController::actionReport() */
	public const ROUTE_REPORT = '/lead/report/report';

	private FunctionalTester $tester;

	public function _before(FunctionalTester $I): void {
		$this->tester = $I;
	}

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
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Old Status');
		$I->seeInGridHeader('Answers');
		$I->seeInGridHeader('Details');
	}

	public function checkReport(LeadManager $I): void {
		$I->haveFixtures(array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		));
		$I->amLoggedIn();

		$lead = $this->grabLead();
		$I->amOnRoute(static::ROUTE_REPORT, ['id' => $lead->getId()]);
		$I->see('Create Report for Lead: ' . $lead->getName(), 'h1');
		$I->seeOptionIsSelected('#reportform-status_id', LeadStatus::getNames()[$lead->getStatusId()]);
		$I->fillField('Details', 'Some Report Details');
		$I->click('Save');
		$I->seeRecord(LeadReport::class, [
			'lead_id' => $lead->getId(),
			'details' => 'Some Report Details',
		]);
		$I->seeInCurrentUrl(LeadCest::ROUTE_VIEW);
		$I->see('Some Report Details');
	}

	private function grabLead(string $index = 'new-wordpress-accident'): ActiveLead {
		return $this->tester->grabFixture(LeadFixtureHelper::LEAD, $index);
	}

}
