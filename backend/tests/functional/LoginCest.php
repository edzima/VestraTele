<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;
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
				'class' => UserFixture::className(),
				'dataFile' => codecept_data_dir() . 'login_data.php',
			],
		];
	}

	public function loginUserWithoutPermision(FunctionalTester $I) {
		$user = $I->grabFixture('user', 0);
		Yii::$app->authManager->revokeAll($user->id);
		$I->amOnPage('/site/login');
		$I->fillField('Username', 'erau');
		$I->fillField('Password', 'password_0');
		$I->click('#login-form button[type=submit]');
		$I->see('Login');
	}

	public function loginUser(FunctionalTester $I) {
		$user = $I->grabFixture('user', 0);
		Yii::$app->authManager->assign(Yii::$app->authManager->getPermission('loginToBackend'), $user->id);
		$I->amOnPage('/site/login');
		$I->fillField('Username', 'erau');
		$I->fillField('Password', 'password_0');
		$I->click('#login-form button[type=submit]');

		$I->see('Logout', 'a[data-method="post"]');
		$I->dontSeeLink('Login');
		$I->dontSeeLink('Signup');
	}

}
