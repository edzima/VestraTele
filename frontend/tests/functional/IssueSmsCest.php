<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\helpers\Flash;
use common\models\issue\IssueUser;
use common\models\user\Worker;
use frontend\tests\_support\CustomerServiceTester;
use frontend\tests\_support\IssueUserTester;
use frontend\tests\FunctionalTester;

class IssueSmsCest {

	/**
	 * @see SmsController::actionPush()
	 */
	private const ROUTE_PUSH = '/issue-sms/push';
	private const ROUTE_ISSUE_INDEX = IssueCest::ROUTE_INDEX;
	private const ROUTE_ISSUE_VIEW = IssueCest::ROUTE_VIEW;

	private const LINK_TEXT = 'Send SMS';
	private const SELECTOR_FORM = '#issue-sms-push-form';

	private IssueFixtureHelper $issueFixture;

	public function _before(FunctionalTester $I): void {
		$this->issueFixture = new IssueFixtureHelper($I);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::note(),
			UserFixtureHelper::profile(UserFixtureHelper::WORKER_AGENT),
		);
	}

	public function checkWithoutPermission(IssueUserTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
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

	public function checkWithPermissionForNotSelfIssue(IssueUserTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_AGNES_MILLER);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seePageNotFound();
	}

	public function checkWithPermission(IssueUserTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
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

	public function checkInvalidUserType(IssueUserTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1, 'userType' => 'not-existed-user']);
		$I->seePageNotFound();
	}

	public function checkSendSelf(IssueUserTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(['48122222300'], 'Self Message'));
		$I->seeValidationError('Phones numbers is invalid.');
		$I->dontSeeJobIsPushed();
	}

	public function checkPushWithUserType(IssueUserTester $I): void {
		$issue = $this->issueFixture->grabIssue(1);
		$I->amLoggedInAs($issue->getIssueModel()->agent->id);
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => $issue->getIssueId(), 'userType' => IssueUser::TYPE_CUSTOMER]);
		$I->seeInTitle('Send SMS for Issue: ' . $issue->getIssueName() . ' - ' . IssueUser::getTypesNames()[IssueUser::TYPE_CUSTOMER]);
		$I->fillField('Message', 'Test message');
		$I->click(static::LINK_TEXT, static::SELECTOR_FORM);
		$I->seeInCurrentUrl(static::ROUTE_ISSUE_VIEW);
		$I->seeFlash('Success push SMS: Test message to send queue for Issue', Flash::TYPE_SUCCESS);
		$I->seeJobIsPushed();
	}

	public function checkSubmitMultipleUsers(CustomerServiceTester $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_SMS);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(['48673222220', '48673222110'], 'Test Message'));
		$I->seeJobIsPushed(2);
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
