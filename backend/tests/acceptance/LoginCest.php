<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use backend\tests\Step\acceptance\Admin;
use backend\tests\Step\acceptance\Manager;
use Yii;

/**
 * Class LoginCest
 */
class LoginCest {

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
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'login_data.php',
			],
		];
	}

	public function loginUserWithoutPermision(AcceptanceTester $I) {
		Yii::$app->authManager->revokeAll(1);
		$I->amOnPage('/site/login');
		$I->fillField('Username', 'erau');
		$I->fillField('Password', 'password_0');
		$I->click('#login-form button[type=submit]');
		$I->see('Login');
	}

	public function loginUserWithPermission(AcceptanceTester $I) {
		$user = $I->grabFixture('user', 0);
		Yii::$app->authManager->assign(Yii::$app->authManager->getPermission('loginToBackend'), $user->id);
		$I->amOnPage('/site/login');
		$I->fillField('Username', 'erau');
		$I->fillField('Password', 'password_0');
		$I->click('#login-form button[type=submit]');

		$I->waitForElementNotVisible('#logout-link');
		$I->dontSeeLink('Login');
		$I->dontSeeLink('Signup');
	}

	public function loginAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->waitForElementNotVisible('#logout-link');
		$I->dontSeeLink('Login');
	}

	public function loginAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->waitForElementNotVisible('#logout-link');
		$I->dontSeeLink('Login');
	}

}
