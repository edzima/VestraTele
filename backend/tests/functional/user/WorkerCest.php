<?php

namespace backend\tests\functional\user;

use backend\modules\user\controllers\WorkerController;
use backend\tests\functional\provision\ProvisionUserCest;
use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\WorkersManager;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;

/**
 * Class WorkerCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class WorkerCest {

	/* @see WorkerController::actionIndex() */
	protected const ROUTE_INDEX = '/user/worker/index';
	/* @see WorkerController::actionCreate() */
	protected const ROUTE_CREATE = '/user/worker/create';
	/* @see WorkerController::actionHierarchy() */
	protected const ROUTE_HIERARCHY = '/user/worker/hierarchy';

	public function _fixtures(): array {
		return array_merge(
			UserFixtureHelper::workers(),
			UserFixtureHelper::profile(UserFixtureHelper::WORKER_AGENT)
		);
	}

	public function checkIndexAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Workers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Workers', 'h1');
		$I->dontSeeLink('Create worker');
		$I->see('No results found.');
		$I->amOnRoute(static::ROUTE_INDEX, ['WorkerUserSearch[lastname]' => 'Now']);
		$I->see('Peter');
		$I->see('Nowak');
		$I->seeElement('a', ['title' => 'View']);
		$I->dontSeeElement('a', ['title' => 'Update']);
		$I->dontSeeElement('a', ['title' => 'Provisions']);
		$I->dontSeeElement('a', ['title' => 'Hierarchy']);
		$I->dontSeeElement('a', ['title' => 'Delete']);
	}

	public function checkIndexPageAsWorkerManager(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Workers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Workers', 'h1');
		$I->seeInGridHeader('Firstname');
		$I->seeInGridHeader('Lastname');
		$I->seeInGridHeader('Email');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Status');
		$I->dontSeeInGridHeader('Ip');
		$I->seeInGridHeader('Action at');
		$I->seeElement('a', ['title' => 'Update']);
		$I->seeElement('a', ['title' => 'View']);
		$I->seeElement('a', ['title' => 'Delete']);

		$I->dontSeeElement('a', ['title' => 'Provisions']);
		$I->dontSeeElement('a', ['title' => 'Hierarchy']);
	}

	public function checkIndexPageAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Workers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Workers', 'h1');
		$I->seeInGridHeader('Firstname');
		$I->seeInGridHeader('Lastname');
		$I->seeInGridHeader('Email');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Ip');
		$I->seeInGridHeader('Action at');
		$I->seeElement('a', ['title' => 'View']);
		$I->seeElement('a', ['title' => 'Update']);
		$I->seeElement('a', ['title' => 'Delete']);
		$I->seeElement('a', ['title' => 'Provisions']);
		$I->seeElement('a', ['title' => 'Hierarchy']);
	}

	public function checkVisibleHierarchyActionButtonInIndexPage(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeElement('a', ['title' => 'Hierarchy']);

		$I->assignPermission(User::PERMISSION_WORKERS_HIERARCHY);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeElement('a', ['title' => 'Hierarchy']);
	}

	public function checkProvisionAsWorkerManager(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([ProvisionUserCest::ROUTE_USER_VIEW, 'userId' => $I->grabFixture(UserFixtureHelper::WORKER_TELEMARKETER, 0)->id]);
		$I->seeResponseCodeIs(403);
	}

	public function checkHierarchyAsWorkerManager(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_HIERARCHY, 'id' => $I->grabFixture(UserFixtureHelper::WORKER_TELEMARKETER, 0)->id]);
		$I->seeResponseCodeIs(403);
	}

	public function checkCreateLink(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Create worker');
		$I->click('Create worker');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function checkCreateAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->seeResponseCodeIs(403);
	}

	public function checkCreateAsWorkersManager(WorkersManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Workers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Workers', 'h1');
		$I->seeLink('Create worker');
		$I->click('Create worker');
		$I->see('Create worker', 'h1');
	}

}
