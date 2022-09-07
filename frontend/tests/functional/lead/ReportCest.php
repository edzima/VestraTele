<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\ReportController;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class ReportCest {

	/** @see ReportController::actionIndex() */
	private const ROUTE_INDEX = '/lead/report/index';
	/** @see ReportController::actionReport() */
	private const ROUTE_REPORT = '/lead/report/report';
	/** @see ReportController::actionUpdate() */
	private const ROUTE_UPDATE = '/lead/report/update';
	/** @see ReportController::actionDelete() */
	private const ROUTE_DELETE = '/lead/report/delete';

	private const SELECTOR_FORM = '#lead-report-form';

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Lead Reports');
		$I->seeInLoginUrl();
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuLink('Lead Reports');
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkMenuLink(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeMenuLink('Lead Reports');
		$I->click('Lead Reports');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkReportNotSelfLead(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_REPORT, ['id' => 2]);
		$I->see('Create Report');
	}

	public function checkReportSelfLeadPage(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_REPORT, ['id' => 1]);
		$I->see('Create Report for Lead: John');
		$I->see('Status');
		$I->see('Type');
		$I->see('Phone');
		$I->see('Email');
		$I->see('Provider');
	}

	public function checkReportSelfLead(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_REPORT, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM, [
			'ReportForm[status_id]' => LeadStatusInterface::STATUS_ARCHIVE,
			'ReportForm[details]' => 'Change status to archive.',
		]);

		$I->seeInCurrentUrl(Url::toRoute([LeadCest::ROUTE_VIEW, 'id' => 1]));
		$I->seeRecord(LeadReport::class, [
			'lead_id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'details' => 'Change status to archive.',
		]);
	}

	public function checkUpdateSelfReport(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_UPDATE, ['id' => 1]);
		$I->see('Update Lead Report: 1');
	}

	public function checkUpdateNotSelfReport(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_UPDATE, ['id' => 3]);
		$I->seePageNotFound();
	}

	public function checkDeleteSelfReport(FunctionalTester $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => 2]), $I->getCSRF());
		$I->dontSeeRecord(LeadReport::class, [
			'id' => 2,
		]);
	}

	private function fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		);
	}
}
