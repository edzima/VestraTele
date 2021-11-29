<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\IssueController;
use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;

class IssueStageChangeCest {

	/** @see IssueController::actionStage() */
	public const ROUTE = '/issue/issue/stage';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::stageAndTypesFixtures()
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
	}
}
