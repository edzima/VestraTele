<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\Manager;
use common\fixtures\user\CustomerFixture;

/**
 * Class CustomerIndexCest
 */
class CustomerIndexCest {

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures() {
		return [
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => codecept_data_dir() . 'customer.php',
			],
		];
	}

	public function _before(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute('/user/customer/index');
	}

	public function checkTable(FunctionalTester $I): void {
		$I->see('Customers');
		$I->see('Firstname', 'table');
		$I->see('Lastname', 'table');
		$I->see('City', 'table');
		$I->see('Email', 'table');
		$I->see('Phone', 'table');
	}

	public function checkCreateCustomerLink(FunctionalTester $I): void {
		$I->seeLink('Create customer');
		$I->click('Create customer');
		$I->see('Create customer');
	}

}
