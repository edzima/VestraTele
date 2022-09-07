<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\helpers\Flash;
use common\models\user\Worker;
use common\modules\lead\controllers\SmsController;

class SmsCest {

	/** @see SmsController::actionPush() */
	private const ROUTE_PUSH = '/lead/sms/push';

	/** @see SmsController::actionPushMultiple() */
	private const ROUTE_PUSH_MULTIPLE = '/lead/sms/push-multiple';

	private const SELECTOR_FORM_PUSH = '#lead-sms-push-form';
	private const SELECTOR_FORM_PUSH_MULTIPLE = '#lead-multiple-sms-push-form';

	private const PERMISSION_BASE = Worker::PERMISSION_SMS;
	private const PERMISSION_MULTIPLE = Worker::PERMISSION_MULTIPLE_SMS;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::user(),
		);
	}

	public function tryPushWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkPushPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->see('Send SMS to Lead: John');
	}

	public function checkLeadPageWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(LeadCest::ROUTE_VIEW, ['id' => 1]);
		$I->dontSeeLink('Send SMS');
	}

	public function checkLeadPageWithPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->amOnRoute(LeadCest::ROUTE_VIEW, ['id' => 1]);
		$I->seeLink('Send SMS');
		$I->click('Send SMS');
		$I->seeInCurrentUrl(static::ROUTE_PUSH);
	}

	public function tryPushEmpty(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->amOnRoute(static::ROUTE_PUSH, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM_PUSH, $this->formSingleParams(null, null));
		$I->seeValidationError('Message cannot be blank.');
	}

	public function tryPushMultipleWithoutBasePermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_PUSH_MULTIPLE, ['ids' => [1, 2]]);
		$I->seeResponseCodeIs(403);
	}

	public function tryPushMultipleWithBasePermissionWithoutMulti(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->amOnRoute(static::ROUTE_PUSH_MULTIPLE, ['ids' => [1, 2]]);
		$I->seeResponseCodeIs(403);
	}

	public function tryPushMultipleWithBaseAndMultiPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->assignPermission(static::PERMISSION_MULTIPLE);
		$I->amOnRoute(static::ROUTE_PUSH_MULTIPLE, ['ids' => [1, 2]]);
		$I->see('Send multiple SMS to 2 Leads');
		$I->submitForm(static::SELECTOR_FORM_PUSH_MULTIPLE, $this->formMultipleParams(2, 'Test Multiple MSG'));
		$I->seeJobIsPushed(2);
		$I->seeFlash('Success add: 2 SMS: Test Multiple MSG to send queue.', Flash::TYPE_SUCCESS);
	}

	public function tryPushMultipleAsSingle(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION_BASE);
		$I->assignPermission(static::PERMISSION_MULTIPLE);
		$I->amOnRoute(static::ROUTE_PUSH_MULTIPLE, ['ids' => [1]]);
		$I->seeInCurrentUrl(static::ROUTE_PUSH);
	}

	private function formSingleParams($statusId, $message, $withOverwrite = true): array {
		return [
			"LeadSmsForm[status_id]" => $statusId,
			"LeadSmsForm[message]" => $message,
			"LeadSmsForm[with_overwrite]" => $withOverwrite,
		];
	}

	private function formMultipleParams($statusId, $message, $withOverwrite = true): array {
		return [
			"LeadMultipleSmsForm[status_id]" => $statusId,
			"LeadMultipleSmsForm[message]" => $message,
			"LeadMultipleSmsForm[with_overwrite]" => $withOverwrite,
		];
	}
}
