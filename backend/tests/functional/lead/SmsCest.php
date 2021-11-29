<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\Worker;
use common\modules\lead\controllers\SmsController;

class SmsCest {

	/** @see SmsController::actionPush() */
	private const ROUTE = '/lead/sms/push';
	private const SELECTOR_FORM = '#lead-sms-push-form';

	private const PERMISSION = Worker::PERMISSION_SMS;

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
		$I->amOnRoute(static::ROUTE, ['id' => 1]);
		$I->seeResponseCodeIs(403);
	}

	public function checkPushPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE, ['id' => 1]);
		$I->see('Send SMS to Lead: John');
	}

	public function checkLeadPageWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(LeadCest::ROUTE_VIEW, ['id' => 1]);
		$I->dontSeeLink('Send SMS');
	}

	public function checkLeadPageWithPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(LeadCest::ROUTE_VIEW, ['id' => 1]);
		$I->seeLink('Send SMS');
		$I->click('Send SMS');
		$I->seeInCurrentUrl(static::ROUTE);
	}

	public function tryPushEmpty(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE, ['id' => 1]);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(null, null));
		$I->seeValidationError('Message cannot be blank.');
		$I->seeValidationError('Status cannot be current Status: New');
	}

	private function formParams($statusId, $message, $withOverwrite = true): array {
		return [
			"LeadSmsForm[status_id]" => $statusId,
			"LeadSmsForm[message]" => $message,
			"LeadSmsForm[with_overwrite]" => $withOverwrite,
		];
	}
}
