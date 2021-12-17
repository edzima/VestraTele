<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\user\Worker;
use frontend\controllers\IssueController;
use frontend\tests\_support\CustomerServiceTester;

class IssueStageCest {

	/** @see IssueController::actionStage() */
	public const ROUTE = '/issue/stage';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::users(),
		);
	}

	public function checkAsCustomerServiceWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'issueId' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCustomerServiceWithPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_ISSUE_STAGE_CHANGE);
		$I->amOnPage([static::ROUTE, 'issueId' => 1]);
		$I->see('Change Stage: ');
	}

	public function sendEmpty(): void {

	}
}
