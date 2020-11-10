<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\MeetIssueManager;

class MeetCreateCest {

	public const ROUTE = '/issue/meet/create';

	public function _before(MeetIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE);
	}

	public function checkPage(MeetIssueManager $I): void {
		$I->see('Create meet');
	}
}
