<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SummonController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\SummonIssueManager;

class SummonCest {

	/** @see SummonController::actionIndex() */
	public const ROUTE_INDEX = '/issue/summon/index';

	/** @see SummonController::actionCreate() */
	public const ROUTE_CREATE = '/issue/summon/create';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueSummonManager(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Summons');
		$I->seeLink('Create summon');

		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Term');
		$I->seeInGridHeader('Title');
		$I->seeInGridHeader('Start at');
		$I->seeInGridHeader('Realized at');
		$I->seeInGridHeader('Deadline at');
		$I->seeInGridHeader('Updated at');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Contractor');
	}

	public function checkCreateSummonLink(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->click('Create summon');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}
}
