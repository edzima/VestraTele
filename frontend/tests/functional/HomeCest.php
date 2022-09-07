<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\ArticleFixtureHelper;
use frontend\tests\FunctionalTester;
use Yii;

class HomeCest {

	public function _fixtures(): array {
		return ArticleFixtureHelper::fixtures();
	}

	public function checkOpen(FunctionalTester $I) {
		$I->amOnPage(Yii::$app->homeUrl);
		$I->see('Vestra');
		$I->seeLink('Login');
		$I->click('Login');
		$I->see('Login', 'h1');
	}

	public function checkAsGuestNewsVisible(FunctionalTester $I): void {
		$I->amOnRoute(Yii::$app->homeUrl);
		$I->dontSee('Visible on Mainpage Article');
	}

	public function checkNotAsGuestNewsVisible(FunctionalTester $I): void {
		$I->amLoggedIn();
		$I->amOnPage(Yii::$app->homeUrl);
		$I->see('Visible on Mainpage Article');
		$I->dontSee('Draft on Mainpage Article');
	}
}
