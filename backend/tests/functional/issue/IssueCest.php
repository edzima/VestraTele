<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;

class IssueCest {

	protected const ROUTE_INDEX = '/issue/issue/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSee('Issues', 'h1');
	}

	public function checkWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Issues', 'h1');
	}

	public function checkIndex(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Issues', 'h1');
	}

}
