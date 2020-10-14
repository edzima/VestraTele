<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\IssueManager;
use common\fixtures\user\CustomerFixture;
use common\models\user\Customer;

class IssueCreateCest {

	protected const ROUTE_CREATE = '/issue/issue/create';

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

	public function checkCreate(IssueManager $I): void {
		$I->amLoggedIn();
		/** @var Customer $customer */
		$customer = $I->grabFixture('customer', 0);

		$I->amOnRoute('/issue/issue/create', ['customerId' => $customer->id]);
		$I->see('Create issue for: ' . $customer, 'title');
	}
}
