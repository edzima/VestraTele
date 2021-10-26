<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\NoteController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueNote;
use common\models\issue\IssueSettlement;
use common\models\issue\Summon;
use frontend\helpers\Html;

class NoteCest {

	/** @see NoteController::actionIndex() */
	public const ROUTE_INDEX = '/issue/note/index';
	/** @see NoteController::actionCreate() */
	private const ROUTE_CREATE = '/issue/note/create';
	/** @see NoteController::actionCreateSettlement() */
	private const ROUTE_CREATE_SETTLEMENT = '/issue/note/create-settlement';
	/** @see NoteController::actionCreateSummon() */
	public const ROUTE_CREATE_SUMMON = '/issue/note/create-summon';
	/** @see NoteController::actionUpdate() */
	private const ROUTE_UPDATE = '/issue/note/update';

	private const SELECTOR_FORM = '#issue-note-form';

	public function checkIndexAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndexAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIssueIndexPageWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(IssueIndexCest::ROUTE);
		$I->dontSeeLink('Issue Notes');
	}

	public function checkIssueIndexPageWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(IssueIndexCest::ROUTE);
		$I->seeLink('Issue Notes');
		$I->click('Issue Notes');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndexGridColumns(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Issue Notes');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Title');
		$I->seeInGridHeader('Description');
		$I->seeInGridHeader('Is Pinned');
		$I->seeInGridHeader('Is Template');
		$I->seeInGridHeader('Publish At');
		$I->seeInGridHeader('Created At');
		$I->seeInGridHeader('Updated At');
	}

	public function checkCreateEmpty(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(static::ROUTE_CREATE, ['issueId' => 1]);
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams('', '', ''));
		$I->seeValidationError('Title cannot be blank');
		$I->seeValidationError('Publish At cannot be blank');
	}

	public function actionCreateWithoutPublishAt(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(static::ROUTE_CREATE, ['issueId' => 1]);
		$I->see('Create Issue Note');
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title',
			'Some Description',
		)
		);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Some Title',
			'description' => 'Some Description',
		]);
	}

	public function actionCreateWithPublishAt(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(static::ROUTE_CREATE, ['issueId' => 1]);
		$I->see('Create Issue Note');
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title',
			'Some Description',
			'2020-02-02 10:00:00'
		)
		);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Some Title',
			'description' => 'Some Description',
			'publish_at' => '2020-02-02 10:00:00',
		]);
	}

	public function checkUpdate(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		$I->amOnRoute(static::ROUTE_UPDATE, ['id' => 1]);
		$I->see('Update Issue Note');
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Some Title Updated',
			'Some Description',
			'2020-02-02 10:00:10'
		)
		);
		$I->seeRecord(IssueNote::class, [
			'title' => 'Some Title Updated',
			'description' => 'Some Description',
			'publish_at' => '2020-02-02 10:00:10',
		]);
	}

	public function checkUpdateSmsNote(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		/** @var IssueNote $note */
		$note = $I->grabFixture(IssueFixtureHelper::NOTE, 'sms_1');
		$I->amOnRoute(static::ROUTE_UPDATE, ['id' => $note->id]);
		$I->seePageNotFound();
	}

	public function checkUpdateChangeStageNote(IssueManager $I): void {
		$I->haveFixtures($this->fixtures());
		$I->amLoggedIn();
		$I->assignNotePermission();
		/** @var IssueNote $note */
		$note = $I->grabFixture(IssueFixtureHelper::NOTE, 'stage-change');
		$I->amOnRoute(static::ROUTE_UPDATE, ['id' => $note->id]);
		$I->see('Update Issue Note');
		$I->seeInField(['name' => 'IssueNoteForm[title]'], $note->title);
		$I->seeElement('input', [
			'name' => 'IssueNoteForm[title]',
			'disabled' => '1',
		]);
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Try change Title',
		)
		);

		$I->dontSeeRecord(IssueNote::class, [
			'id' => $note->id,
			'title' => 'Try change Title',
		]);
		$I->seeRecord(IssueNote::class, [
			'id' => $note->id,
			'title' => $note->title,
		]);
	}

	public function checkCreateForSummon(IssueManager $I): void {
		$I->haveFixtures(array_merge($this->fixtures(), IssueFixtureHelper::summon()));
		$I->amLoggedIn();
		$I->assignNotePermission();
		/** @var Summon $summon */
		$summon = $I->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$I->amOnRoute(static::ROUTE_CREATE_SUMMON, ['id' => $summon->id]);
		$I->see('Create Issue Note for Summon: ' . $summon->title);
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Summon Title',
			'Some Description',
		)
		);
		$model = IssueNote::find()->andWhere(['title' => 'Summon Title'])->asArray()->all();
		$I->seeRecord(IssueNote::class, [
			'title' => 'Summon Title',
			'description' => 'Some Description',
			'type' => IssueNote::generateType(IssueNote::TYPE_SUMMON, $summon->id),
		]);
	}

	public function checkCreateForSettlement(IssueManager $I): void {
		$I->haveFixtures(array_merge($this->fixtures(), SettlementFixtureHelper::settlement()));
		$I->amLoggedIn();
		$I->assignNotePermission();
		/** @var IssueSettlement $settlement */
		$settlement = $I->grabFixture(SettlementFixtureHelper::SETTLEMENT, 'not-payed-with-double-costs');
		$I->amOnRoute(static::ROUTE_CREATE_SETTLEMENT, ['id' => $settlement->getId()]);
		$I->see('Create Issue Note for settlement: ' . $settlement->getTypeName());
		$I->submitForm(static::SELECTOR_FORM, $this->formsParams(
			'Settlement Title Updated',
			'Some Description',
		)
		);
		$I->seeRecord(IssueNote::class, [
			'title' => 'Settlement Title Updated',
			'description' => 'Some Description',
			'type' => IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, $settlement->id),
		]);
	}

	private function formsParams($title, $description = null, $publish_at = null, $is_pinned = null) {
		$params = [
			'IssueNoteForm[title]' => $title,
			'IssueNoteForm[description]' => $description,
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

	public function fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			IssueFixtureHelper::users(),
		);
	}

}
