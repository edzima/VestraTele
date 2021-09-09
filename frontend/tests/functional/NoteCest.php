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

	private const SELECTOR_FORM = '#issue-note-form';

	public function checkIssueAsCustomerServiceWithoutPermission(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_ISSUE);
		$I->seeResponseCodeIs(403);
	}

	public function checkIssueAsCustomerService(CustomerServiceTester $I): void {
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::types(),
				IssueFixtureHelper::note(),
			)
		);
		$I->assignPermission(User::PERMISSION_NOTE);
		$I->amLoggedIn();
		/** @var Issue $model */
		$model = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $model->id]);
		$I->see('Create Issue Note for: ' . $model->longId);
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title',
			'Some Description')
		);
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
		$I->see('Create Issue Note for settlement: ' . $model->getTypeName());
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title',
			'Some Description')
		);
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
		$I->see('Create Issue Note for Summon: ' . $model->title);
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title',
			'Some Description')
		);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => $model->issue_id,
			'title' => 'Some title',
			'description' => 'Some description',
			'type' => IssueNote::generateType(IssueNote::TYPE_SUMMON, $model->id),
		]);
		$I->seeInCurrentUrl(SummonCest::ROUTE_VIEW);
	}

	private function formsParams($title, $description = null, $publish_at = null, $is_pinned = null) {
		$params = [
			'IssueNoteForm[title]' => $title,
		];
		if ($description !== null) {
			$params['IssueNoteForm[description]'] = $description;
		}
		if ($publish_at !== null) {
			$params['IssueNoteForm[publish_at]'] = $publish_at;
		}
		if ($is_pinned !== null) {
			$params['IssueNoteForm[is_pinned]'] = $is_pinned;
		}
		return $params;
	}
}
