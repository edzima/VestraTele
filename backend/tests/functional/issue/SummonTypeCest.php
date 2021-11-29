<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SummonTypeController;
use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class SummonTypeCest {

	/** @see SummonTypeController::actionIndex() */
	public const ROUTE_INDEX = '/issue/summon-type/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsSummonIssueManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsSummonIssueManagerWithManagerPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SUMMON_MANAGER);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Summon Types');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Short Name');
		$I->seeInGridHeader('Title');
		$I->seeInGridHeader('Term');
	}
}
