<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\Worker;
use common\modules\lead\controllers\StatusController;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadStatusInterface;

class StatusCest {

	/** @see StatusController::actionIndex() */
	private const ROUTE_INDEX = '/lead/status/index';
	/** @see StatusController::actionCreate() */
	private const ROUTE_CREATE = '/lead/status/create';

	/** @see StatusController::actionChange() */
	private const ROUTE_CHANGE = '/lead/status/change';

	protected const FORM_CREATE_SELECTOR = '#lead-status-form';
	protected const FORM_CHANGE_SELECTOR = '#lead-status-change-form';

	const PERMISSION = Worker::PERMISSION_LEAD_STATUS;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::reports()
		);
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Statuses');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuLink('Statuses');
		$I->clickMenuLink('Statuses');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Statuses', 'h1');
	}

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Description');
		$I->seeInGridHeader('Short Report');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateWithOnlyName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_CREATE_SELECTOR, $this->formParams('Some Name'));
		$I->seeRecord(LeadStatus::class, [
			'name' => 'Some Name',
		]);
	}

	public function checkCreateWithFilterColor(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR,
			array_merge(
				$this->formParams('Status Filter Color'),
				$this->filterFormParams('#6fa8dc')
			)
		);
		$I->seeRecord(LeadStatus::class, [
			'name' => 'Status Filter Color',
		]);
		$I->see('#6fa8dc');
	}

	public function checkCreateWithShorReportName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_CREATE_SELECTOR, $this->formParams('Short Report Status', true));
		$I->seeRecord(LeadStatus::class, [
			'name' => 'Short Report Status',
			'short_report' => true,
		]);
	}

	public function checkChangeStatusForLeads(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CHANGE, [
			'ids' => [1, 2],
		]);
		$I->see('Change Status for Leads: 2');
		$I->submitForm(static::FORM_CHANGE_SELECTOR, [
			'LeadStatusChangeForm[status_id]' => LeadStatusInterface::STATUS_ARCHIVE,
		]);

		$I->seeRecord(Lead::class, [
			'id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$I->seeRecord(LeadReport::class, [
			'lead_id' => 1,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'owner_id' => $I->getUser()->id,
		]);
		$I->seeRecord(Lead::class, [
			'id' => 2,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
	}

	private function formParams($name = null, $short_report = null) {
		return [
			'LeadStatus[name]' => $name,
			'LeadStatus[short_report]' => $short_report,
		];
	}

	private function filterFormParams($color): array {
		return [
			'FilterOptions[color]' => $color,
		];
	}

}
