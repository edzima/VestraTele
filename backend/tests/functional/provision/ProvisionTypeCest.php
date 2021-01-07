<?php

namespace backend\tests\functional\provision;

use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;

class ProvisionTypeCest {

	public const ROUTE_INDEX = '/provision/type/index';
	public const ROUTE_CREATE = '/provision/type/create';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Provisions');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Provisions');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Provisions types', 'h1');
	}

	public function checkCreateLink(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Create');
		$I->click('Create');
	}

	public function checkGridContent(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Value');
		$I->seeInGridHeader('Is percentage');
		$I->seeInGridHeader('Only with telemarketer');
		$I->seeInGridHeader('Roles');
		$I->seeInGridHeader('Issue types');
	}

	public function checkCreate(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->fillField('Name', 'name');
		$I->fillField('Provision value', 25);
		$I->click('Save');
		$I->see('name', 'h1');
	}
}
