<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\modules\lead\controllers\AnswerController;

class AnswerCest {

	/* @see AnswerController::actionIndex() */
	public const ROUTE_INDEX = '/lead/answer/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Answers');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Answers');
		$I->clickMenuLink('Answers');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead answers', 'h1');
	}

	public function checkIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Lead');
		$I->seeInGridHeader('Question');
		$I->seeInGridHeader('Answer');
		$I->seeInGridHeader('Old status');
		$I->seeInGridHeader('New status');
	}

}
