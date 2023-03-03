<?php

namespace frontend\tests\functional;

use common\models\user\User;
use frontend\controllers\ArticleController;
use frontend\tests\FunctionalTester;

class ArticleCest {

	/** @see ArticleController::actionIndex() */
	public const ROUTE_INDEX = '/article/index';

	const PERMISSION = User::PERMISSION_NEWS;

	public function checkMenuLinkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Articles');
	}

	public function checkIndexPageWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLinkWithPermission(FunctionalTester $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuLink('Articles');
		$I->clickMenuLink('Articles');
		$I->amOnPage(static::ROUTE_INDEX);
	}

}
