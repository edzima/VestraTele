<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\Manager;
use common\models\user\Worker;

class MessageCest {

	private const ROUTE_INDEX = '/message-templates/default/index';

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Message Templates');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_MESSAGE_TEMPLATE);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeMenuSubLink('Message Templates');
		$I->clickMenuSubLink('Message Templates');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('list of templates');
		$I->seeLink('Create template');
	}
}
