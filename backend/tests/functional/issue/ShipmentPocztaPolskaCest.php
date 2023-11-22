<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\ShipmentPocztaPolskaController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class ShipmentPocztaPolskaCest {

	/** @see ShipmentPocztaPolskaController::actionIndex() */
	public const ROUTE_INDEX = '/issue/shipment-poczta-polska/index';
	public const PERMISSION = Worker::PERMISSION_ISSUE_SHIPMENT;

	public function checkIndexRouteAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Issue Shipment Poczta Polska');
		$I->see(403);
	}

	public function checkIndexRouteAsIssueManagerWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Issue Shipment Poczta Polska');
		$I->see(403);
	}

	public function checkIndexRouteAsIssueManagerWithPermission(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->clickMenuSubLink('Issue Shipment Poczta Polska');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->seeInTitle('Issue Shipment Poczta Polska');
	}
}
