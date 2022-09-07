<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\IssueController;
use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;

class IssueStageChangeCest {

	/** @see IssueController::actionStage() */
	public const ROUTE = '/issue/issue/stage';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::linkedIssues(),
			IssueFixtureHelper::users(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_STAGE_CHANGE),
		);
	}

	public function checkStageWithoutStageParam(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'issueId' => 1]);
		$I->selectOption('Stage', 2);
		$I->click('Save');
		$I->seeRecord(Issue::class, [
			'id' => 1,
			'stage_id' => 2,
		]);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'user_id' => $I->getUser()->id,
			'title' => 'Proposal (previous: Completing documents)',
		]);
		$I->seeEmailIsSent();
	}

	public function checkStageChangeWithLinkedIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'issueId' => 1]);
		$I->selectOption('Stage', 2);
		$I->selectOption('Linked Issues', [2, 4]);
		$I->see('Linked Issues');
		$I->see('Change Stage also in Linked Issues.');
		$I->click('Save');
		$I->seeRecord(Issue::class, [
			'id' => 1,
			'stage_id' => 2,
		]);

		$I->seeRecord(Issue::class, [
			'id' => 2,
			'stage_id' => 2,
		]);

		$I->seeRecord(Issue::class, [
			'id' => 4,
			'stage_id' => 2,
		]);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'user_id' => $I->getUser()->id,
			'title' => 'Proposal (previous: Completing documents)',
		]);

		//Issue: 2 Already Has Stage Id: 2
		$I->dontSeeRecord(IssueNote::class, [
			'issue_id' => 2,
			'user_id' => $I->getUser()->id,
			'title' => 'Proposal (previous: Completing documents)',
		]);

		$I->seeRecord(IssueNote::class, [
			'issue_id' => 4,
			'user_id' => $I->getUser()->id,
			'title' => 'Proposal (previous: Stage with min 2 calculation)',
		]);
		$I->seeEmailIsSent();
	}

	public function checkStageChangeWithoutLinkedIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'issueId' => 3]);
		$I->dontSee('Linked Issues');
		$I->dontSee('Change Stage also in Linked Issues.');
	}

	public function checkStageWithStageParam(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'issueId' => 1, 'stageId' => 2]);
		$I->click('Save');
		$I->seeRecord(Issue::class, [
			'id' => 1,
			'stage_id' => 2,
		]);
		$I->haveRecord(IssueNote::class, [
			'issue_id' => 1,
			'user_id' => $I->getUser()->id,
			'title' => 'Proposal (previous: Completing documents)',
		]);
		$I->seeEmailIsSent();
	}
}
