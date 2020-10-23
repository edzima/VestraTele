<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\Manager;
use common\fixtures\UserFixture;
use common\models\user\User;
use common\models\user\UserProfile;

/**
 * Class CustomerCreateCest
 */
class CustomerCreateCest {

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

	public function _before(Manager $I) {
		$I->amLoggedIn();
		$I->amOnRoute('/user/customer/create');
	}

	public function checkEmpty(FunctionalTester $I): void {
		$I->fillField('Email', '');
		$I->fillField('Firstname', '');
		$I->fillField('Lastname', '');

		$this->sendForm($I);
		$I->seeValidationError('Firstname cannot be blank.');
		$I->seeValidationError('Lastname cannot be blank.');

	}

	public function checkOnlyFirstname(FunctionalTester $I): void {
		$I->fillField('Firstname', 'some_firstname');
		$this->sendForm($I);
		$I->dontSeeValidationError('Firstname cannot be blank.');
		$I->seeValidationError('Lastname cannot be blank.');
	}

	public function checkOnlyLastname(FunctionalTester $I): void {
		$I->fillField('Lastname', 'some_lastname');
		$this->sendForm($I);
		$I->dontSeeValidationError('Lastname cannot be blank.');
		$I->seeValidationError('Firstname cannot be blank.');
	}

	public function checkCorrectWithoutEmail(FunctionalTester $I): void {
		$I->fillField('Firstname', 'Fred');
		$I->fillField('Lastname', 'Johansson');
		$this->sendForm($I);
		$I->dontSeeValidationError('Firstname cannot be blank.');
		$I->dontSeeValidationError('Lastname cannot be blank.');
		$I->dontSeeEmailIsSent();

		$I->seeRecord(User::class, [
			'and',
			['like', 'username', 'FJ%', false],
			['status' => User::STATUS_INACTIVE],
		]);
		$I->seeRecord(UserProfile::class, [
			'firstname' => 'Fred',
			'lastname' => 'Johansson',
		]);
	}

	public function checkCorrectWithSendEmail(FunctionalTester $I): void {
		$I->fillField('Email', 'fred@test.com');
		$I->fillField('Firstname', 'Fred');
		$I->fillField('Lastname', 'Johansson');

		$this->sendForm($I);
		$I->dontSeeValidationError('Firstname cannot be blank.');
		$I->dontSeeValidationError('Lastname cannot be blank.');

		$I->seeRecord(User::class, [
			'and',
			['like', 'username', 'FJ%', false],
			['status' => User::STATUS_INACTIVE],
		]);

		$I->seeRecord(UserProfile::class, [
			'firstname' => 'Fred',
			'lastname' => 'Johansson',
		]);

		$I->seeEmailIsSent();
		$mail = $I->grabLastSentEmail();
		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('fred@test.com');
	}

	public function checkInvalidEmail(FunctionalTester $I): void {
		$I->fillField('Email', 'not-address-email');
		$this->sendForm($I);
		$I->dontSeeEmailIsSent();
		$I->seeValidationError('Email is not a valid email address.');
	}

	private function sendForm(FunctionalTester $I): void {
		$I->click('#customer-form button[type=submit]');
	}

}
