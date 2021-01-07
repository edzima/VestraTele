<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\Manager;

class SummonCest {

	public const PAGE = '/issue/summon/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::PAGE);
		$I->seeResponseCodeIs(403);
	}
}
