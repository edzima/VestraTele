<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

class HomeCest {

	public function checkOpen(FunctionalTester $I) {
		$I->amOnPage(\Yii::$app->homeUrl);
		$I->see('Vestra');
		$I->seeLink('Login');
		$I->click('Login');
		$I->see('Login', 'h1');
	}
}
