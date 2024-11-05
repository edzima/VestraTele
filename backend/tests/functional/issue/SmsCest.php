<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SmsController;
use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\helpers\Flash;
use common\models\issue\IssueUser;
use common\models\user\Worker;

class SmsCest {

	/**
	 * @see SmsController::actionPush()
	 */
	private const ROUTE_PUSH = '/issue/sms/push';
	private const ROUTE_ISSUE_INDEX = IssueIndexCest::ROUTE;
	private const ROUTE_ISSUE_VIEW = IssueViewCest::ROUTE;

	private const LINK_TEXT = 'Send SMS';
	private const SELECTOR_FORM = '#issue-sms-push-form';

	private IssueFixtureHelper $issueFixture;

	public function _before(IssueManager $I): void {
		$this->issueFixture = new IssueFixtureHelper($I);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::note(),
			UserFixtureHelper::profile(UserFixtureHelper::WORKER_AGENT),
		);
	}

	public function checkWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->wantTo('Check Access to Push Page');
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeResponseCodeIs(403);

		$I->wantTo('Check dont visible Link on Issue Index Page');
		$I->amOnRoute(static::ROUTE_ISSUE_INDEX);
		$I->dontSeeGridActionLink(static::LINK_TEXT);

		$I->wantTo('Check dont visible Link on Issue View Page');
		$I->amOnRoute(static::ROUTE_ISSUE_VIEW, ['id' => 1]);
		$I->dontSeeLink(static::LINK_TEXT);
	}

	public function checkWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		IssueFixtureHelper::accessUserTypes($I->getUser()->id);
		$I->wantTo('Check Access to Push Page');
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->see('Send SMS for Issue: ');

		$I->wantTo('Check visible Link on Issue Index Page');
		$I->amOnRoute(static::ROUTE_ISSUE_INDEX);
		$I->seeGridActionLink(static::LINK_TEXT);
		$I->clickGridActionLink(static::LINK_TEXT);
		$I->seeInCurrentUrl(static::ROUTE_PUSH);

		$I->wantTo('Check visible Link on Issue View Page');
		$I->amOnRoute(static::ROUTE_ISSUE_VIEW, ['id' => 1]);
		$I->seeLink(static::LINK_TEXT);
		$I->click(static::LINK_TEXT, '.issue-view');
		$I->seeInCurrentUrl(static::ROUTE_PUSH);
	}

	public function checkInvalidUserType(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1, 'userType' => 'not-existed-user']);
		$I->seePageNotFound();
	}

	public function checkTitleWithoutUserType(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$issue = $this->issueFixture->grabIssue(1);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => $issue->getIssueId()]);
		$I->seeInTitle('Send SMS for Issue: ' . $issue->getIssueName());
	}

	public function checkPushWithUserType(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$issue = $this->issueFixture->grabIssue(1);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => $issue->getIssueId(), 'userType' => IssueUser::TYPE_CUSTOMER]);
		$I->seeInTitle('Send SMS for Issue: ' . $issue->getIssueName() . ' - ' . IssueUser::getTypesNames()[IssueUser::TYPE_CUSTOMER]);
		$I->fillField('Message', 'Test message');
		$I->click('Send SMS', static::SELECTOR_FORM);
		$I->seeInCurrentUrl(static::ROUTE_ISSUE_VIEW);
		$I->seeFlash('Success push SMS: Test message to send queue for Issue', Flash::TYPE_SUCCESS);
		$I->seeJobIsPushed();
	}

	public function checkSubmitEmpty(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeInField('Note Title', 'SMS Sent');
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('', '', ''));
		$I->seeValidationError('Message cannot be blank.');
	}

	public function checkSubmitToNotThisIssueNumber(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->wantToTest('Check Customer Phone from other Issue');
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(['48682222110'], 'Test Message'));
		$I->seeValidationError('Phones numbers is invalid.');
		$I->dontSeeJobIsPushed();
	}

	public function checkSubmitMultipleUsers(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(['48673222220', '48673222110'], 'Test Message'));
		$I->seeJobIsPushed(2);
	}

	public function checkUserTypeWithoutPhones(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$issue = $this->issueFixture->grabIssue(1);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => $issue->getIssueId(), 'userType' => IssueUser::TYPE_AGENT]);
		$I->seeInCurrentUrl(static::ROUTE_ISSUE_VIEW);
	}

	private function formParams($phone, $message, $noteTitle = ''): array {
		$params = [
			'IssueSmsForm[message]' => $message,
			'IssueSmsForm[note_title]' => $noteTitle,
		];
		$params[] = $message;
		if (is_array($phone)) {
			$params['IssueSmsForm[phones]'] = $phone;
		} else {
			$params['IssueSmsForm[phone]'] = $phone;
		}
		return $params;
	}
}
