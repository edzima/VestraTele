<?php

namespace backend\tests\unit;

use backend\modules\user\models\CustomerUserForm;
use backend\tests\UnitTester;
use common\fixtures\user\CustomerFixture;
use common\models\user\Customer;
use common\models\user\User;

class CustomerFormTest extends \Codeception\Test\Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	public function _before() {
		$this->tester->haveFixtures([
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => codecept_data_dir() . 'customer.php',
			],
		]);
	}

	public function testPolishCharsInNames(): void {
		$model = new CustomerUserForm();
		$profile = $model->getProfile();
		$profile->firstname = 'Łukasz';
		$profile->lastname = 'Śws';
		$this->tester->assertTrue($model->save());
	}

	public function testCorrectCreate() {
		$model = new CustomerUserForm();

		$model->email = 'test@email.com';
		$profile = $model->getProfile();
		$profile->firstname = 'some_firstname';
		$profile->lastname = 'some_lastname';

		$this->tester->assertTrue($model->save());
		/** @var User $user */
		$user = $this->tester->grabRecord(Customer::class, [
			'email' => 'test@email.com',
			'status' => User::STATUS_INACTIVE,
		]);
		expect($user)->isInstanceOf(Customer::class);
		$this->assertSame('some_firstname', $user->profile->firstname);
		$this->assertSame('some_lastname', $user->profile->lastname);

		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('test@email.com');
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
	}

	public function testUpdate(): void {
		$model = new CustomerUserForm();
		$user = $this->tester->grabFixture('customer', 0);
		$model->setModel($user);
		$model->username = 'new_username';
		$model->getProfile()->lastname = 'lastname';
		$model->getProfile()->firstname = 'firstname';
		expect($model->save())->true();
		/** @var User $user */
		$user = $this->tester->grabRecord(Customer::class, ['username' => 'new_username']);
		expect($user)->isInstanceOf(Customer::class);
		$this->tester->assertSame('firstname', $user->profile->firstname);
		$this->tester->assertSame('lastname', $user->profile->lastname);
	}

}
