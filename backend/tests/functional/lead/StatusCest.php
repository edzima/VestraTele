<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\modules\lead\controllers\StatusController;
use common\modules\lead\models\LeadStatus;

class StatusCest {

	/** @see StatusController::actionIndex() */
	private const ROUTE_INDEX = '/lead/status/index';
	/** @see StatusController::actionCreate() */
	private const ROUTE_CREATE = '/lead/status/create';

	protected const FORM_SELECTOR = '#lead-status-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Statuses');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Statuses');
		$I->clickMenuLink('Statuses');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Statuses', 'h1');
	}

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Description');
		$I->seeInGridHeader('Short Report');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateWithOnlyName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('Some Name'));
		$I->seeRecord(LeadStatus::class, [
			'name' => 'Some Name',
		]);
	}

	public function checkCreateWithShorReportName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('Short Report Status', true));
		$I->seeRecord(LeadStatus::class, [
			'name' => 'Short Report Status',
			'short_report' => true,
		]);
	}

	private function formParams($name = null, $short_report = null) {
		return [
			'LeadStatus[name]' => $name,
			'LeadStatus[short_report]' => $short_report,
		];
	}

}
