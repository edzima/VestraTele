<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;
use common\models\user\User;
use common\models\user\Worker;
use frontend\controllers\SummonController;
use frontend\tests\_support\CustomerServiceTester;

class SummonCest {

	/** @see SummonController::actionIndex() */
	public const ROUTE_INDEX = '/summon/index';

	/** @see SummonController::actionCreate() */
	public const ROUTE_CREATE = '/summon/create';

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
		$I->dontSee('Contractor');
	}

	public function checkCreateWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_CREATE, 'issueId' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkCreateWithPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SUMMON);
		$I->assignPermission(Worker::PERMISSION_SUMMON_CREATE);
		$I->amOnPage([static::ROUTE_CREATE, 'issueId' => 1]);
		$I->see('Create Summon');
		$I->submitForm('#summon-form', [
			'SummonForm[issue_id]' => 1,
			'SummonForm[type_id]' => 1,
			'SummonForm[contractor_id]' => $I->getUser()->id,
			'SummonForm[term]' => 3,
			'SummonForm[title]' => 'Test Summon Without Issue in Route Param',
			'SummonForm[city_id]' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,

		]);
		$I->seeRecord(Summon::class, [
			'issue_id' => 1,
			'type_id' => 1,
			'title' => 'Test Summon Without Issue in Route Param',
		]);
		$I->seeEmailIsSent();
	}

	public function checkUpdateNotSelfSummon(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkUpdateSelfSummonAsOwner(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		/** @var Summon $summon */
		$summonId = $I->haveRecord(Summon::class, [
			'owner_id' => $I->getUser()->id,
			'issue_id' => 1,
			'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			'entity_id' => 1,
			'contractor_id' => UserFixtureHelper::TELE_1,
			'title' => 'New summon',
			'status' => Summon::STATUS_NEW,
			'type_id' => 1,
		]);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summonId]);
		$I->see('Update summon: New summon');
		$I->selectOption('Status', Summon::STATUS_IN_PROGRESS);
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
		$I->see(Summon::getStatusesNames()[Summon::STATUS_IN_PROGRESS]);
	}

	public function checkUpdateNotSelfSummonWithSummonManagerPermission(CustomerServiceTester $I): void {
		$I->assignPermission(Worker::PERMISSION_SUMMON);
		$I->assignPermission(Worker::PERMISSION_SUMMON_MANAGER);
		$I->amLoggedIn();
		/** @var Summon $summon */
		$summonId = $I->haveRecord(Summon::class, [
			'owner_id' => UserFixtureHelper::AGENT_EMILY_PAT,
			'issue_id' => 1,
			'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			'entity_id' => 1,
			'contractor_id' => UserFixtureHelper::TELE_1,
			'title' => 'New summon',
			'status' => Summon::STATUS_NEW,
			'type_id' => 1,
		]);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summonId]);
		$I->see('Update summon: New summon');
		$I->selectOption('Status', Summon::STATUS_IN_PROGRESS);
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
		$I->see(Summon::getStatusesNames()[Summon::STATUS_IN_PROGRESS]);
	}

	public function checkUpdateSelfSummonAsContractor(CustomerServiceTester $I): void {
		$I->assignPermission(User::PERMISSION_SUMMON);
		$I->amLoggedIn();
		/** @var Summon $summon */
		$summonId = $I->haveRecord(Summon::class, [
			'owner_id' => 300,
			'issue_id' => 1,
			'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			'entity_id' => 1,
			'contractor_id' => $I->getUser()->id,
			'title' => 'New summon',
			'status' => Summon::STATUS_NEW,
			'type_id' => 1,
		]);
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summonId]);
		$I->seeResponseCodeIs(403);
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
