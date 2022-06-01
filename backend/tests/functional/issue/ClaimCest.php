<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\ClaimController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class ClaimCest {

	/** @see ClaimController::actionIndex() */
	public const ROUTE_INDEX = '/issue/claim/index';

	/** @see ClaimController::actionCreate() */
	public const ROUTE_CREATE = '/issue/claim/create';

	public const PERMISSION = Worker::PERMISSION_ISSUE_CLAIM;

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Issue Claims');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Issue Claims');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithPermission(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Issue Claims');
		$I->clickMenuSubLink('Issue Claims');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}
}
