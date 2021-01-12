<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SummonController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Summon;
use common\models\user\Worker;

class SummonCest {

	/** @see SummonController::actionIndex() */
	public const ROUTE_INDEX = '/issue/summon/index';

	/** @see SummonController::actionCreate() */
	public const ROUTE_CREATE = '/issue/summon/create';

	/** @see SummonController::actionView() */
	public const ROUTE_VIEW = '/issue/summon/view';

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

	public function checkView(SummonIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::summon(),
		));
		/** @var Summon $summon */
		$summon = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $summon->id]);
		$I->see('Summon #' . $summon->id);
		$I->dontSeeLink('Create note');
		$I->see($summon->title);
		$I->see($summon->issue->longId);
		$I->see($summon->owner->getFullName());
		$I->see($summon->contractor->getFullName());
		$I->see($summon->typeName);
		$I->see($summon->statusName);
		$I->see($summon->termName);
		$I->see($summon->entityWithCity);
	}

	public function checkViewWithNotePermission(SummonIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::summon(),
		));
		/** @var Summon $summon */
		$summon = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->assignPermission(Worker::PERMISSION_NOTE);
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $summon->id]);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeInCurrentUrl(NoteCest::ROUTE_CREATE_SUMMON);
	}
}
