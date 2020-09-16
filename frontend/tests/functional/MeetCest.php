<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use common\models\user\User;
use frontend\tests\FunctionalTester;
use Yii;

/**
 * Class MeetCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class MeetCest {

	private const ROLE_NAME = User::ROLE_MEET;

	public function _fixtures(): array {
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		];
	}

	public function checkAccessWithoutLogin(FunctionalTester $I): void {
		$I->amOnRoute('/meet/index');
		$I->seeInLoginUrl();
	}

	public function checkAccessWithoutPermission(FunctionalTester $I) {
		$user = $I->grabFixture('user', 0);
		$I->amLoggedInAs($user);
		if (Yii::$app->authManager->checkAccess($user->id, static::ROLE_NAME)) {
			Yii::$app->authManager->revoke(Yii::$app->authManager->getRole(static::ROLE_NAME), $user->id);
		}
		$I->amOnRoute('/meet/index');
		$I->seeResponseCodeIs(403);
	}

	public function checkAccessWithPermission(FunctionalTester $I): void {
		$user = $I->grabFixture('user', 0);
		$I->amLoggedInAs($user);
		if (!Yii::$app->authManager->checkAccess($user->id, static::ROLE_NAME)) {
			Yii::$app->authManager->assign(Yii::$app->authManager->getRole(static::ROLE_NAME), $user->id);
		}
		$I->amOnRoute('/meet/index');
		$I->seeResponseCodeIs(200);
		$I->see('Lead', 'h1');
	}

}
