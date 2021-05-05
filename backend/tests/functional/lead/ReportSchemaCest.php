<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\modules\lead\controllers\ReportSchemaController;
use common\modules\lead\models\LeadReportSchema;

class ReportSchemaCest {

	/* @see ReportSchemaController::actionIndex() */
	public const ROUTE_INDEX = '/lead/report-schema/index';
	/* @see ReportSchemaController::actionCreate() */
	private const ROUTE_CREATE = '/lead/report-schema/create';

	private const FORM_SELECTOR = '#lead-report-schema-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Reports schemas');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Reports schemas');
		$I->clickMenuLink('Reports schemas');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInTitle('Lead Report Schemas');

		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Placeholder');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Is required');
		$I->seeInGridHeader('Show in grid');
	}

	public function checkCreateOnlyName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->see('Create Lead Report Schema');
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('New lead report schema', null, null));
		$I->seeRecord(LeadReportSchema::class, [
			'name' => 'New lead report schema',
		]);
	}

	private function formParams($name, $placeholder, $is_required) {
		return [
			'LeadReportSchemaForm[name]' => $name,
			'LeadReportSchemaForm[placeholder]' => $placeholder,
			'LeadReportSchemaForm[is_required]' => $is_required,
		];
	}

}
