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

	public function checkIndexPage(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Shortname');
		$I->seeInGridHeader('VAT (%)');
		$I->seeInGridHeader('Provision');
		$I->seeInGridHeader('With additional Date');
		$I->seeInGridHeader('Meet');
	}

	public function checkCreate(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some new type name',
			'SNTM',
			0,
		)
		);
		$I->seeRecord(IssueType::class, [
			'name' => 'Some new type name',
			'short_name' => 'SNTM',
			'vat' => 0,
		]);
		$I->seeInTitle('Some new type name');
	}

	private function formParams($name, $shortname, $vat, $parent_id = null): array {
		return [
			'IssueTypeForm[name]' => $name,
			'IssueTypeForm[short_name]' => $shortname,
			'IssueTypeForm[vat]' => $vat,
			'IssueTypeForm[parent_id]' => $parent_id,
		];
	}

}
