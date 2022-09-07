<?php

namespace backend\tests\unit\user;

use backend\modules\user\models\UserForm;
use backend\tests\unit\Unit;
use common\fixtures\AddressFixture;
use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\fixtures\user\UserAddressFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfileFixture;
use common\fixtures\UserTraitAssignFixture;
use common\models\user\User;
use common\models\user\UserTraitAssign;
use Yii;

class UserFormTest extends Unit {

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
				[
					'user' => [
						'class' => UserFixture::class,
						'dataFile' => codecept_data_dir() . 'user.php',
					],
					'user-profile' => [
						'class' => UserProfileFixture::class,
						'dataFile' => codecept_data_dir() . 'user_profile.php',
					],
					'user-address' => [
						'class' => UserAddressFixture::class,
						'dataFile' => codecept_data_dir() . 'user_address.php',
					],
					'address' => [
						'class' => AddressFixture::class,
						'dataFile' => codecept_data_dir() . 'address.php',
					],
					'user-trait' => [
						'class' => UserTraitAssignFixture::class,
						'dataFile' => codecept_data_dir() . 'user_trait.php',
					],
					'trait' => UserFixtureHelper::traits(),
				],
				TerytFixtureHelper::fixtures()
			)
		);
	}

	public function testCorrectCreate(): void {
		$model = new UserForm();
		$model->sendEmail = true;

		$model->username = 'some_username';
		$model->email = 'test@email.com';
		$model->password = 'some_password';
		$profile = $model->getProfile();
		$profile->firstname = 'some_firstname';
		$profile->lastname = 'some_lastname';
		$address = $model->getHomeAddress();
		$address->postal_code = '34-100';
		$this->tester->assertTrue($model->save());

		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, [
			'username' => 'some_username',
			'email' => 'test@email.com',
			'status' => User::STATUS_INACTIVE,
		]);
		expect($user)->isInstanceOf(User::class);
		$this->tester->assertSame('some_firstname', $user->profile->firstname);
		$this->tester->assertSame('some_lastname', $user->profile->lastname);

		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('test@email.com');
		expect($mail->getFrom())->hasKey(Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . Yii::$app->name);
		//l	expect($mail->toString())->stringContainsString($user->verification_token);

	}

	public function testUpdate(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->username = 'new_username';
		$model->getProfile()->lastname = 'lastname';
		$model->getProfile()->firstname = 'firstname';
		expect($model->save())->true();
		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, ['username' => 'new_username']);
		expect($user)->isInstanceOf(User::class);
		$this->tester->assertSame('firstname', $user->profile->firstname);
		$this->tester->assertSame('lastname', $user->profile->lastname);
	}

	public function testInvalidTrait(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [121212];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Traits is invalid.', $model->getFirstError('traits'));
	}

	public function testAssignTraitToUser(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [UserTraitAssign::TRAIT_BAILIFF];
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
	}

	public function testAssignTraitsToUser(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [UserTraitAssign::TRAIT_COMMISSION_REFUND, UserTraitAssign::TRAIT_BAILIFF];
		$this->tester->assertTrue($model->validate());
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
	}

	public function testAssignTraitAsEmptyArray(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$model->setModel($user);
		$model->traits = [];
		$this->tester->assertTrue($model->save());
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => 2]);
	}

	public function testAssignTraitAsEmptyString(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$model->setModel($user);
		$model->traits = '';
		$this->tester->assertTrue($model->save());
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => 2]);
	}
}
