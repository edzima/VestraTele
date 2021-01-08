<?php

namespace backend\tests\functional\user;

use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;
use common\models\user\UserTrait;

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
		return [
			'customer' => UserFixtureHelper::customer(),
			'customer-profile' => UserFixtureHelper::customerProfile(),
			'customer-traits' => UserFixtureHelper::customerTraits(),
		];
	}

	public function checkLinks(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$I->seeLink('Update');
		$I->dontSeeLink('Add issue');
		$I->dontSeeLink('Link to issue');
	}

	public function checkUpdateLink(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$I->seeLink('Update');
		$I->click('Update');
		$I->see('Update customer');
	}

	public function checkLinkToIssueLinkPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$I->seeLink('Link to issue');
		$I->click('Link to issue');
		$I->see('Link Wayne John to issue');
	}

	public function checkBaseInfo(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$I->see('Wayne John', 'h1');
		$I->see('john@wayne.com');
		$I->see('+48 673 222 110');
		$I->see('customer.wayne');
		$I->see(User::getStatusesNames()[User::STATUS_ACTIVE]);
		$I->see(UserTrait::getNames()[UserTrait::TRAIT_BAILIFF]);
		$I->dontSee(UserTrait::getNames()[UserTrait::TRAIT_ANTYVINDICATION]);

		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID]);
		$I->see('Larson Erika', 'h1');
	}

	public function checkIssue(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE, 'id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]);
		$I->see('Issues', 'legend');
		$I->see('Issue');
		$I->see('Signature act');
		$I->see('As role');
		$I->see('Agent');
	}

}
