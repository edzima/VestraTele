<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
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

	public function tryChange(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_ISSUE_STAGE_CHANGE);
		/** @var Issue $issue */
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE, 'issueId' => $issue->getIssueId()]);
		$I->submitForm('#issue-stage-form', [
			'IssueStageChangeForm[stage_id]' => 2,
		]);

		$I->seeInCurrentUrl(IssueViewCest::ROUTE_VIEW);
		$I->seeRecord(Issue::class, [
			'id' => $issue->getIssueId(),
			'stage_id' => 2,
		]);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => $issue->getIssueId(),
			'type' => IssueNote::generateType(
				IssueNote::generateType(IssueNote::TYPE_STAGE_CHANGE, 2),
				1
			),
		]);
	}
}
