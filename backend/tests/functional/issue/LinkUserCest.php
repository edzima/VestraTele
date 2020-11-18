<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;

class LinkUserCest {

	protected const FORM_SELECTOR = '#issue-user-form';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures() {
		return IssueFixtureHelper::fixtures();
	}

	public function checkPage(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(['issue/user/link', 'userId' => 101]);
		$I->see('Link Larson Erika to issue', 'h1');
	}

	public function checkEmptyFields(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(['issue/user/link', 'userId' => 101]);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('', ''));
		$I->seeValidationError('Issue cannot be blank.');
		$I->seeValidationError('As role cannot be blank.');
	}

	public function checkChangeUserId(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(['issue/user/link', 'userId' => 101]);
		$params = $this->formParams(1, 'victim');
		$params['IssueUserForm[user_id]'] = 102;
		$I->submitForm(static::FORM_SELECTOR, $params);
		$I->dontSee('Victim - Jons Tommy');
		$I->see('Victim - Larson Erika');
	}

	public function checkLinkToExistedIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(['issue/user/link', 'userId' => 101]);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(1, 'victim'));
		$I->see('Victim - Larson Erika');
	}

	public function checkLinkToArchiveIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(['issue/user/link', 'userId' => 101]);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(5, 'victim'));
		$I->seeValidationError('Issue cannot be archived.');
	}

	/**
	 * @param $issue_id
	 * @param $type
	 * @return array
	 */
	protected function formParams($issue_id, $type): array {
		return [
			'IssueUserForm[issue_id]' => $issue_id,
			'IssueUserForm[type]' => $type,
		];
	}

}
