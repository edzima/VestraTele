<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\ArchiveController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class ArchiveCest {

	/** @see ArchiveController::actionIndex() */
	public const ROUTE_INDEX = '/issue/archive/index';

	public const PERMISSION = Worker::PERMISSION_ARCHIVE;

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Archive');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Archive');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithPermission(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Archive');
		$I->clickMenuSubLink('Archive');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}
}
