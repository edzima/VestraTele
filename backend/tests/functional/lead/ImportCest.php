<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\helpers\Flash;
use common\models\user\Worker;
use common\modules\lead\controllers\ImportController;
use common\modules\lead\models\Lead;

class ImportCest {

	/** @see ImportController::actionCsv() */
	public const ROUTE_CSV = '/lead/import/csv';

	private const PERMISSION = Worker::PERMISSION_LEAD_IMPORT;
	private const FORM_SELECTOR = '#lead-import-csv-form';

	public function checkWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Import Leads');
		$I->amOnRoute(static::ROUTE_CSV);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Import Leads');
		$I->clickMenuSubLink('Import Leads');
		$I->seeInCurrentUrl(static::ROUTE_CSV);
	}

	public function checkEmpty(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CSV);
		$I->submitForm(static::FORM_SELECTOR, []);
		$I->seeValidationError('Please upload a file.');
		$I->seeValidationError('Source cannot be blank.');
	}

	public function checkCentralPhone(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CSV);
		$I->attachFile('File', 'lead/central-phone.csv');
		$I->submitForm(static::FORM_SELECTOR, [
			'LeadCSVImport[source_id]' => 1,
			'LeadCSVImport[provider]' => Lead::PROVIDER_CENTRAL_PHONE,
			'LeadCSVImport[phoneColumn]' => 1,
			'LeadCSVImport[nameColumn]' => '',
			'LeadCSVImport[dateColumn]' => 0,
		]);
		$I->seeRecord(Lead::class, [
			'source_id' => 1,
			'name' => 'central-phone.2',
		]);
		$I->seeFlash('Success Import: 4 from CSV.', Flash::TYPE_SUCCESS);
	}

}
