<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\MeetIssueManager;

class MeetCest {

	public const ROUTE_INDEX = '/issue/meet/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Meets');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Meets');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsMeetManager(MeetIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Meets');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Meets');
	}

}
