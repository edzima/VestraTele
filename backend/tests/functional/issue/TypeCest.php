<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\TypeController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueType;
use common\models\user\Worker;

class TypeCest {

	/** @see TypeController::actionIndex() */
	private const ROUTE_INDEX = '/issue/type/index';
	/** @see TypeController::actionCreate() */
	private const ROUTE_CREATE = '/issue/type/create';

	private const FORM_SELECTOR = '#issue-type-form';

	private const PERMISSION = Worker::PERMISSION_ISSUE_TYPE_MANAGER;

	public function _fixtures(): array {
		return IssueFixtureHelper::types();
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Types');
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Types');
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithTypePermission(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Types');
		$I->clickMenuSubLink('Types');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPageWithoutIssueTypePermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Shortname');
		$I->seeInGridHeader('With additional Date');
		$I->seeInGridHeader('Type Parent');
		$I->dontSeeInGridHeader('Roles');
		$I->dontSeeInGridHeader('Permissions');
	}

	public function checkIndexPageWithIssueTypePermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->assignPermission(Worker::PERMISSION_ISSUE_TYPE_PERMISSIONS);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Roles');
		$I->seeInGridHeader('Permissions');
	}


	public function checkCreate(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some new type name',
			'SNTM',
		)
		);
		$I->seeRecord(IssueType::class, [
			'name' => 'Some new type name',
			'short_name' => 'SNTM',
		]);
		$I->seeInTitle('Some new type name');
	}

	private function formParams($name, $shortname, $parent_id = null): array {
		return [
			'IssueTypeForm[name]' => $name,
			'IssueTypeForm[short_name]' => $shortname,
			'IssueTypeForm[parent_id]' => $parent_id,
		];
	}

}
