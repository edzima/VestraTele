<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadUser;

class UserCest {

	/* @see UserController::actionIndex() */
	public const ROUTE_INDEX = '/lead/user/index';
	/* @see UserController::actionAssign() */
	public const ROUTE_ASSIGN = '/lead/user/assign';

	private const SELECTOR_ASSIGN_FORM = '#leads-user-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Users');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Users');
		$I->clickMenuLink('Users');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Lead Users', 'h1');
	}

	public function checkIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Lead');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Type');
	}

	public function checkAssign(LeadManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amOnRoute(static::ROUTE_ASSIGN);
		$I->submitForm(static::SELECTOR_ASSIGN_FORM, $this->assignFormParams([1, 2], 1, LeadUser::TYPE_TELE));
		$I->seeRecord(LeadUser::class, [
			'lead_id' => 1,
			'user_id' => 1,
			'type' => LeadUser::TYPE_TELE,
		]);
		$I->seeRecord(LeadUser::class, [
			'lead_id' => 2,
			'user_id' => 1,
			'type' => LeadUser::TYPE_TELE,
		]);
		$I->seeEmailIsSent(2);
	}

	public function checkAssignFromLeadIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->sendAjaxPostRequest(LeadCest::ROUTE_INDEX, array_merge([
			'selection' => [1, 2],
		], $I->getCSRF()));
		$I->seeResponseCodeIsRedirection();
	}

	private function assignFormParams(array $leadsIds, int $userId, string $type): array {
		return [
			'LeadsUserForm[leadsIds]' => $leadsIds,
			'LeadsUserForm[userId]' => $userId,
			'LeadsUserForm[type]' => $type,
		];
	}

}
