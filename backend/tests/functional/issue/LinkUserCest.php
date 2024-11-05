<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\UserController;
use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueUser;

class LinkUserCest {

	/** @see UserController::actionLink() */
	public const ROUTE_LINK = 'issue/user/link';
	protected const FORM_SELECTOR = '#issue-user-form';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures(true);
	}

	public function _before(IssueManager $I): void {
		IssueFixtureHelper::accessUserTypes($I->getUser()->getId());
	}

	public function checkPage(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$I->see('Link Larson Erika to issue', 'h1');
	}

	public function checkEmptyFields(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('', ''));
		$I->seeValidationError('Issue cannot be blank.');
		$I->seeValidationError('As role cannot be blank.');
	}

	public function checkChangeUserId(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$params = $this->formParams(1, IssueUser::TYPE_VICTIM);
		$params['IssueUserForm[user_id]'] = UserFixtureHelper::CUSTOMER_TOMMY_JOHNS;
		$I->submitForm(static::FORM_SELECTOR, $params);
		$I->see('Victim - Johns Tommy');
		$I->dontSee('Victim - Larson Erika');
	}

	public function checkLinkToExistedIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(1, IssueUser::TYPE_VICTIM));
		$I->see('Victim - Larson Erika');
	}

	public function checkLinkToArchiveIssue(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		/** @var Issue $issue */
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 'archived');
		$I->submitForm(static::FORM_SELECTOR, $this->formParams($issue->id, IssueUser::TYPE_VICTIM));
		$I->seeValidationError('Issue cannot be archived.');
	}

	public function checkDoubleLink(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(1, IssueUser::TYPE_VICTIM));
		$I->see('Victim - Larson Erika');
		$I->amOnPage($this->linkPage(UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID));
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(2, IssueUser::TYPE_VICTIM));
		$I->see('Victim - Larson Erika');
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

	protected function linkPage(int $userId): array {
		return [static::ROUTE_LINK, 'userId' => $userId];
	}

}
