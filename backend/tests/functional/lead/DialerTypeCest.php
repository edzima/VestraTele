<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\DialerTypeController;
use common\modules\lead\models\LeadDialerType;

class DialerTypeCest {

	/**
	 * @see DialerTypeController::actionIndex()
	 */
	const ROUTE_INDEX = '/lead/dialer-type/index';
	/**
	 * @see DialerTypeController::actionCreate()
	 */
	const ROUTE_CREATE = '/lead/dialer-type/create';

	private const PERMISSION = User::PERMISSION_LEAD_DIALER_MANAGER;
	private const SELECTOR_FORM = '#lead-dialer-type-form';

	public function checkWithLeadPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithLeadDialerPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_LEAD_DIALER);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithLeadDialerManagerPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Lead Dialer Types');
		$I->seeLink('Create Lead Dialer Type');
		$I->click('Create Lead Dialer Type');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function checkIndexGrid(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Count');
	}

	public function checkCreate(LeadManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->haveFixtures($this->fixtures());
		$I->amOnPage(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams(
			'New Dialer Type',
			1,
			LeadDialerType::STATUS_ACTIVE
		));

		$I->seeRecord(LeadDialerType::class, [
			'name' => 'New Dialer Type',
			'status' => LeadDialerType::STATUS_ACTIVE,
			'user_id' => 1,
		]);
	}

	private function formParams($name, $user_id, $status): array {
		return [
			'LeadDialerType[name]' => $name,
			'LeadDialerType[user_id]' => $user_id,
			'LeadDialerType[status]' => $status,
		];
	}

	private function fixtures(): array {
		return array_merge(
			LeadFixtureHelper::dialer(),
			LeadFixtureHelper::user()
		);
	}
}
