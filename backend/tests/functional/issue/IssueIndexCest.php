<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\IssueController;
use backend\tests\_support\Step\Functional\ExportIssueManager;
use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\models\user\Worker;

class IssueIndexCest {

	/**
	 * @see IssueController::actionIndex()
	 */
	public const ROUTE = '/issue/issue/index';

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
		$I->see('Additional Date for Type');
		$I->see('Lawyer');
		$I->see('Agent');
		$I->see('Telemarketer');
		$I->see('Only delayed');
		$I->dontSee('Structures');
		$I->dontSee('Only with payed pay');
		$I->dontSee('Only with all paid Pays');
	}

	public function checkSearchFieldsAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Structures');
	}

	public function checkSearchFieldsWithAllPaidPaysPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_PAY_ALL_PAID);
		$I->amOnRoute(static::ROUTE);
		$I->see('Only with all paid Pays');
	}

	public function checkSearchFieldsWithPayPartPayed(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_PAY_PART_PAYED);
		$I->amOnRoute(static::ROUTE);
		$I->see('Only with payed pay');
	}
}
