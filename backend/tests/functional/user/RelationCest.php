<?php

namespace backend\tests\functional\user;

use backend\modules\user\controllers\RelationController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\UserRelationManager;

class RelationCest {

	/** @see RelationController::actionIndex() */
	private const ROUTE_INDEX = '/user/relation/index';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkIndex(UserRelationManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);

		$I->see('Users - Relations');
		$I->seeLink('Create User Relation');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('To User');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Created At');
		$I->seeInGridHeader('Updated At');
	}
}
