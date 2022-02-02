<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SummonController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;
use common\models\user\Worker;
use Yii;

class SummonCest {

	/** @see SummonController::actionIndex() */
	public const ROUTE_INDEX = '/issue/summon/index';

	/** @see SummonController::actionCreate() */
	public const ROUTE_CREATE = '/issue/summon/create';

	/** @see SummonController::actionUpdate() */
	public const ROUTE_UPDATE = '/issue/summon/update';

	/** @see SummonController::actionView() */
	public const ROUTE_VIEW = '/issue/summon/view';

	public function _before(): void {
		codecept_debug(array_keys($this->_fixtures()));
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::summon()
		);
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndexPageAsIssueSummonManager(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Summons');
		$I->seeLink('Create summon');

		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Title');
		$I->seeInGridHeader('Deadline at');
		$I->seeInGridHeader('Updated at');
		$I->seeInGridHeader('Owner');
		$I->seeInGridHeader('Contractor');
		$I->dontSeeLink('Summon Types');
	}

	public function checkIndexPageAsIssueSummonManagerWithSummonManagerPermission(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SUMMON_MANAGER);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Summon Types');
		$I->click('Summon Types');
		$I->seeInCurrentUrl(SummonTypeCest::ROUTE_INDEX);
	}

	public function checkCreateSummonLink(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->click('Create summon');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function checkCreate(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
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

	public function checkNotUserSummonUpdateWithoutSummonManagerPermission(SummonIssueManager $I): void {
		$I->amLoggedIn();
		/**
		 * @var Summon $summon
		 */
		$summon = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summon->id]);
		$I->seeResponseCodeIs(403);
	}

	public function checkNotUserSummonUpdateWithSummonManagerPermission(SummonIssueManager $I): void {
		$I->assignPermission(Worker::PERMISSION_SUMMON_MANAGER);

		$I->amLoggedIn();
		codecept_debug('Has permission: ' . Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER));

		/**
		 * @var Summon $summon
		 */
		$summon = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnPage([static::ROUTE_UPDATE, 'id' => $summon->id]);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkView(SummonIssueManager $I): void {
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
		$I->see($summon->entityWithCity);
	}

	public function checkViewWithNotePermission(SummonIssueManager $I): void {
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
