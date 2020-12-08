<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\UserController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;

class  IssueUserCest {

	/**
	 * @see UserController::actionIndex()
	 */
	public const ROUTE_INDEX = '/issue/user/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Issue users');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(IssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Issues users');
	}

	public function checkIndex(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Issues users', 'h1');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Surname');
		$I->seeInGridHeader('Type');
	}

	public function checkWithoutArchive(IssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Showing 1-19');
	}

	public function checkWithArchive(IssueManager $I): void {
		$I->assignArchivePermission();
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Showing 1-20 of 23');
	}
}
