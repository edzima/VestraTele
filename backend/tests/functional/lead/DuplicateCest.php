<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\models\user\Worker;
use common\modules\lead\controllers\DuplicateController;

class DuplicateCest {

	/** @see DuplicateController::actionIndex() */
	public const ROUTE_INDEX = '/lead/duplicate/index';

	private const PERMISSION = Worker::PERMISSION_LEAD_DUPLICATE;

	public function checkWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Duplicates');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Duplicates');
		$I->clickMenuSubLink('Duplicates');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkGrid(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Duplicates Leads');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Type');
	}
}
