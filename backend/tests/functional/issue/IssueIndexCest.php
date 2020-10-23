<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;

class IssueIndexCest {

	protected const ROUTE = '/issue/issue/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->dontSee('Issues', 'h1');
	}

	public function checkWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Issues', 'h1');
	}

	public function checkIndex(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Issues', 'h1');
	}

	public function checkSearchFields(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Created at from');
		$I->see('Created at to');
		$I->see('Accident date');
		$I->see('Lawyer');
		$I->see('Agent');
		$I->see('Telemarketer');
		$I->see('Structures');
		$I->see('Only delayed');
	}

}
