<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\Summon;
use common\models\user\User;
use frontend\controllers\NoteController;
use frontend\tests\_support\CustomerServiceTester;

class NoteCest {

	/** @see NoteController::actionIssue() */
	public const ROUTE_ISSUE = '/note/issue';
	/** @see NoteController::actionSettlement() */
	public const ROUTE_SETTLEMENT = '/note/settlement';
	/** @see NoteController::actionSummon() */
	public const ROUTE_SUMMON = '/note/summon';

	public function checkIssueAsCustomerServiceWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_ISSUE);
		$I->seeResponseCodeIs(403);
	}

	public function checkIssueAsCustomerService(CustomerServiceTester $I): void {
		$I->haveFixtures(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note(),
		);
		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		/** @var Issue $model */
		$model = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $model->id]);
		$I->see('Create note for issue: ' . $model->longId);
		$I->fillField('Title', 'Some title');
		$I->fillField('Description', 'Some description');
		$I->click('Save');
		$I->seeRecord(IssueNote::class, [
			'issue_id' => $model->id,
			'title' => 'Some title',
			'description' => 'Some description',
		]);
		$I->seeInCurrentUrl(IssueCest::ROUTE_VIEW);
	}

	public function checkSettlementAsCustomerService(CustomerServiceTester $I): void {
		$settlementFixture = new SettlementFixtureHelper($I);
		$settlementFixture->have(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::agent(),
				IssueFixtureHelper::customer(),
				IssueFixtureHelper::note(),
				SettlementFixtureHelper::settlement()
			)
		);
		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		$model = $settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE_SETTLEMENT, 'id' => $model->id]);
		$I->see('Create note for: ' . $model->getName());
		$I->fillField('Title', 'Some title');
		$I->fillField('Description', 'Some description');
		$I->click('Save');
		$I->seeRecord(IssueNote::class, [
			'issue_id' => $model->issue_id,
			'title' => 'Some title',
			'description' => 'Some description',
			'type' => IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, $model->id),
		]);
		$I->seeInCurrentUrl(SettlementCest::ROUTE_VIEW);
	}

	public function checkSummonAsCustomerService(CustomerServiceTester $I): void {
		$I->haveFixtures(array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::note(),
				IssueFixtureHelper::summon()
			)
		);
		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		/** @var Summon $model */
		$model = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnPage([static::ROUTE_SUMMON, 'id' => $model->id]);
		$I->see('Create note for: ' . $model->getName());
		$I->fillField('Title', 'Some title');
		$I->fillField('Description', 'Some description');
		$I->click('Save');
		$I->seeRecord(IssueNote::class, [
			'issue_id' => $model->issue_id,
			'title' => 'Some title',
			'description' => 'Some description',
			'type' => IssueNote::generateType(IssueNote::TYPE_SUMMON, $model->id),
		]);
		$I->seeInCurrentUrl(SummonCest::ROUTE_VIEW);
	}

}
