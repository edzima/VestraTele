<?php

namespace backend\tests\unit\user;

use backend\modules\user\models\CustomerUserForm;
use backend\tests\unit\Unit;
use common\fixtures\AddressFixture;
use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\UserAddressFixture;
use common\models\user\Customer;
use common\models\user\User;
use Yii;

class CustomerFormTest extends Unit {

	public function _before() {
		parent::_before();
		Yii::$app->authManager->removeAllAssignments();
		$this->tester->haveFixtures(array_merge
			(
				[
					'customer' => [
						'class' => CustomerFixture::class,
						'dataFile' => codecept_data_dir() . 'customer.php',
					],
					'user-address' => [
						'class' => UserAddressFixture::class,
						'dataFile' => codecept_data_dir() . 'customer_address.php',

					],
					'address' => [
						'class' => AddressFixture::class,
						'dataFile' => codecept_data_dir() . 'address.php',
					],
				],
				TerytFixtureHelper::fixtures()
			)
		);
	}

	protected function _after() {
		parent::_after();
		Yii::$app->authManager->removeAllAssignments();
	}

	public function testPolishCharsInNames(): void {
		$model = new CustomerUserForm();
		$profile = $model->getProfile();
		$profile->firstname = 'Łukasz';
		$profile->lastname = 'Śws';

		$this->tester->assertTrue($model->getProfile()->validate());
	}

	public function testCorrectCreate() {
		$model = new CustomerUserForm();

		$model->email = 'test@email.com';
		$profile = $model->getProfile();
		$profile->firstname = 'some_firstname';
		$profile->lastname = 'some_lastname';

		$address = $model->getHomeAddress();
		$address->postal_code = '34-100';
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
		expect($mail->getFrom())->hasKey(Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . Yii::$app->name);
	}

	public function testUpdate(): void {
		$model = new CustomerUserForm();
		$user = $this->tester->grabFixture('customer', 0);
		$model->setModel($user);
		$model->username = 'new_username';
		$model->getProfile()->lastname = 'lastname';
		$model->getProfile()->firstname = 'firstname';
		$model->validate();
		expect($model->save())->true();
		/** @var User $user */
		$user = $this->tester->grabRecord(Customer::class, ['username' => 'new_username']);
		expect($user)->isInstanceOf(Customer::class);
		$this->tester->assertSame('firstname', $user->profile->firstname);
		$this->tester->assertSame('lastname', $user->profile->lastname);
	}

}
