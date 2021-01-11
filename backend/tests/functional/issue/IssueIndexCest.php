<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\IssueController;
use backend\tests\_support\Step\Functional\ExportIssueManager;
use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\SummonIssueManager;

class IssueIndexCest {

	/**
	 * @see IssueController::actionIndex()
	 */
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
		$I->dontSeeLink('CSV export');
		$I->dontSeeLink('Settlements');
		$I->dontSeeLink('Summons');
	}

	public function checkAsIssueSummonManager(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->seeLink('Summons');
		$I->click('Summons');
		$I->seeInCurrentUrl(SummonCest::ROUTE_INDEX);
	}

	public function checkIndexAsExportIssueManager(ExportIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('CSV export');
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
		$I->see('Only delayed');
		$I->dontSee('Structures');
	}

	public function checkSearchFieldsAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Structures');
	}

}
