<?php

namespace acceptance\issue;

use backend\modules\issue\controllers\NoteController;
use backend\tests\AcceptanceTester;
use backend\tests\Step\acceptance\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueNote;
use common\models\user\User;
use yii\helpers\Url;

class NoteCest {

	/** @see NoteController::actionCreate() */
	private const ROUTE_CREATE = '/issue/note/create';

	public function _before(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_NOTE);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note()
		);
	}

	public function checkCreate(AcceptanceTester $I): void {
		$I->amOnPage(Url::to([static::ROUTE_CREATE, 'issueId' => 1]));
		$I->fillOutSelect2OptionField('.field-issuenoteform-title', 'Not Existed Yet Title');
		$I->fillOutSelect2OptionField('.field-issuenoteform-description', 'Not Existed Yet Description');
		$I->click('#issue-note-form button[type=submit]');
		$I->wait(1);
		$I->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Not Existed Yet Title',
			'description' => 'Not Existed Yet Description',
		]);
	}

	public function checkFillSearch(AcceptanceTester $I): void {
		$I->amOnPage(Url::to([static::ROUTE_CREATE, 'issueId' => 1]));
		$I->fillOutSelect2OptionField('.field-issuenoteform-title', 'Not Existed Yet Title');
		$I->see('Not Existed Yet Title');
		$I->seeInSelect2OptionSearchField('.field-issuenoteform-title', 'Not Existed Yet Title');
		$I->fillOutSelect2OptionField('.field-issuenoteform-description', 'Not Existed Yet Description');
		$I->see('Not Existed Yet Description');
		$I->seeInSelect2OptionSearchField('.field-issuenoteform-description', 'Not Existed Yet Description');
	}
}
