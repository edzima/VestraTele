<?php

namespace backend\tests\functional\issue;

use backend\tests\fixtures\IssueFixtureHelper;
use backend\tests\Step\Functional\IssueManager;
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
		return IssueFixtureHelper::fixtures();
	}

	public function checkCreate(IssueManager $I): void {
		$I->amLoggedIn();
		/** @var Customer $customer */
		$customer = $I->grabFixture('customer', 0);

		$I->amOnRoute('/issue/issue/create', ['customerId' => $customer->id]);
		$I->see('Create issue for: ' . $customer, 'title');
	}
	
}
