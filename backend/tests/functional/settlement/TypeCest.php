<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\TypeController;
use backend\modules\settlement\Module;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\SettlementFixtureHelper;

class TypeCest {

	/** @see TypeController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/type/index';

	/** @see TypeController::actionCreate() */
	private const ROUTE_CREATE = '/settlement/type/create';

	public const PERMISSION = Module::ROLE_SETTLEMENT_TYPE_MANAGER;

	public function _fixtures(): array {
		return SettlementFixtureHelper::type();
	}

	public function checkIndexAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuSublink(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Settlement Types');
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeMenuSubLink('Settlement Types');
		$I->clickMenuSubLink('Settlement Types');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkManagerWithPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->seeLink('Create Settlement Type');
		$I->click('Create Settlement Type');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function create(Manager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CREATE);
	}
}
