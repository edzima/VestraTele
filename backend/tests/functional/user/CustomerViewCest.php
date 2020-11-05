<?php

namespace backend\tests\functional\user;

use backend\tests\fixtures\IssueFixtureHelper;
use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\Manager;
use common\models\user\User;

/**
 * Class CustomerViewCest
 *
 */
class CustomerViewCest {

	protected const ROUTE = 'user/customer/view';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function _before(Manager $I): void {
		$I->amLoggedIn();
	}

	public function checkLinks(FunctionalTester $I): void {
		$I->amOnPage([static::ROUTE, 'id' => 100]);
		$I->seeLink('Update');
		$I->seeLink('Add issue');
		$I->seeLink('Link to issue');
	}

	public function checkBaseInfo(FunctionalTester $I): void {
		$I->amOnPage([static::ROUTE, 'id' => 100]);
		$I->see('Wayne John', 'h1');
		$I->see('john@wayne.com');
		$I->see('+48 673 222 110');
		$I->see('customer.wayne');
		$I->see(User::getStatusesNames()[User::STATUS_ACTIVE]);
	}

	public function checkIssue(FunctionalTester $I): void {
		$I->amOnPage([static::ROUTE, 'id' => 100]);
		$I->see('Issues', 'legend');
		$I->see('Issue');
		$I->see('Signature act');
		$I->see('As role');
		$I->see('Agent');
	}

}
