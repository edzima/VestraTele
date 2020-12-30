<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Summon;
use common\models\user\User;
use frontend\controllers\SummonController;
use frontend\tests\_support\CustomerServiceTester;

class SummonCest {

	/** @see SummonController::actionView() */
	public const ROUTE_VIEW = '/summon/view';

	public function checkView(CustomerServiceTester $I): void {
		$I->haveFixtures(array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::summon()
			)
		);
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		/** @var Summon $model */
		$model = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->see($model->title);
		$I->dontSeeLink('Create note');
	}

	public function checkViewWithNotePermission(CustomerServiceTester $I): void {
		$I->haveFixtures(array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::summon()
			)
		);
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->assignPermission(User::PERMISSION_NOTE);

		$I->amLoggedIn();
		/** @var Summon $model */
		$model = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $model->id]);
		$I->see($model->title);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeInCurrentUrl(NoteCest::ROUTE_SUMMON);
	}

}
