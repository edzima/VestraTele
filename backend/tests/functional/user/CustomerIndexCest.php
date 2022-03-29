<?php

namespace backend\tests\functional\user;

use backend\modules\user\controllers\CustomerController;
use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\Manager;
use common\fixtures\user\CustomerFixture;

/**
 * Class CustomerIndexCest
 */
class CustomerIndexCest {

	/** @see CustomerController::actionIndex() */
	public const ROUTE_INDEX = '/user/customer/index';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return [
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => codecept_data_dir() . 'customer.php',
			],
		];
	}

	public function _before(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
	}

	public function checkTable(FunctionalTester $I): void {
		$I->see('Customers');
		$I->seeInGridHeader('Firstname');
		$I->seeInGridHeader('Lastname');
		$I->seeInGridHeader('City');
		$I->seeInGridHeader('Email');
		$I->seeInGridHeader('Phone');
	}

	public function checkCreateCustomerLink(FunctionalTester $I): void {
		$I->seeLink('Create customer');
		$I->click('Create customer');
		$I->see('Create customer');
	}

}
