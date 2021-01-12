<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\issue\Summon;
use common\models\user\User;
use frontend\controllers\SummonController;
use frontend\tests\_support\CustomerServiceTester;

class SummonCest {

	/** @see SummonController::actionIndex() */
	public const ROUTE_INDEX = '/summon/index';

	/** @see SummonController::actionView() */
	public const ROUTE_VIEW = '/summon/view';

	/** @see SummonController::actionUpdate() */
	public const ROUTE_UPDATE = '/summon/update';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::summon(),
		);
	}

	public function checkIndexWithoutSummonPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndex(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Issue');
		$I->see('Customer');
		$I->see('Type');
		$I->see('Status');
		$I->see('Term');
		$I->dontSee('Contractor');
	}

	public function checkUpdateForNotSelfSummon(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkUpdateForSelfSummon(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		/** @var Summon $summon */
		$summonId = $I->haveRecord(Summon::class, [
			'owner_id' => 300,
			'issue_id' => 1,
			'city_id' => TerytFixtureHelper::SIMC_ID_DUCHOWO,
			'entity_id' => 1,
			'contractor_id' => $I->getUser()->id,
			'title' => 'New summon',
			'status' => Summon::STATUS_NEW,
			'type' => Summon::TYPE_DOCUMENTS,
		]);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summonId]);
		$I->see('Update summon: New summon');
		$I->selectOption('Status', Summon::STATUS_IN_PROGRESS);
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
		$I->see(Summon::getStatusesNames()[Summon::STATUS_IN_PROGRESS]);
	}

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
