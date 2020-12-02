<?php

namespace acceptance\provision;

use backend\tests\fixtures\ProvisionFixtureHelper;
use backend\tests\Page\provision\ProvisionTypePage;
use backend\tests\Step\acceptance\Admin;

class ProvisionTypeCreateCest {

	protected const ROUTE_CREATE = '/provision/type/create';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return ProvisionFixtureHelper::typesFixtures();
	}

	public function _before(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
	}

	public function checkOneRole(Admin $I, ProvisionTypePage $page): void {
		$page->fillRequiredFields('For agent', 25);
		$page->fillRole('Agent');
		$I->click('Save');
		$I->waitForText('For agent', 2, 'h1');
		$I->see('Agent');
	}

	public function checkFewRole(Admin $I, ProvisionTypePage $page): void {
		$page->fillRequiredFields('For agents & lawyers', 25);
		$page->fillRole('Agent');
		$page->fillRole('Lawyer');
		$I->click('Save');
		$I->waitForText('For agents & lawyers', 2, 'h1');
		$I->see('Agent, Lawyer');
	}
}
