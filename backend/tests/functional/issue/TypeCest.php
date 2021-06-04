<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\TypeController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\issue\Provision;

class TypeCest {

	/** @see TypeController::actionIndex() */
	private const ROUTE_INDEX = '/issue/type/index';
	/** @see TypeController::actionCreate() */
	private const ROUTE_CREATE = '/issue/type/create';

	private const FORM_SELECTOR = '#issue-type-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Types');
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuSubLink('Types');
		$I->clickMenuSubLink('Types');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(IssueManager $I): void {
		$I->amLoggedIn();
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
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some new type name',
			'SNTM',
			Provision::TYPE_PERCENTAGE,
			0,
		)
		);
		$I->seeInTitle('Some new type name');
	}

	private function formParams($name, $shortname, $provisionType, $vat): array {
		return [
			'IssueType[name]' => $name,
			'IssueType[short_name]' => $shortname,
			'IssueType[provision_type]' => $provisionType,
			'IssueType[vat]' => $vat,
		];
	}

}
