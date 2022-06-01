<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\TagController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;

class TagCest {

	/** @see TagController::actionIndex() */
	const ROUTE_INDEX = '/issue/tag/index';

	public function checkAccessAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Tags');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAccessAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuSubLink('Tags');
		$I->clickMenuSubLink('Tags');
		$I->seeResponseCodeIsSuccessful();
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}
}
