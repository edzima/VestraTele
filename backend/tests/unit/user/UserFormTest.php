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
use common\tests\_support\UnitModelTrait;
use Yii;

class UserFormTest extends Unit {

	use UnitModelTrait;

	private UserForm $model;

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

	public function testDuplicatesWithEmptyNames(): void {
		$this->giveModel();
		$this->tester->assertNull($this->getModel()->getDuplicatesDataProvider());
	}

	public function testDuplicatesFromFixtures(): void {
		$this->giveModel();
		$model = $this->getModel();
		$model->scenario = UserForm::SCENARIO_CREATE;
		$model->username = 'some_username';
		$model->email = 'test@email.com';
		$model->password = 'some_password';
		$profile = $model->getProfile();
		$profile->firstname = 'John';
		$profile->lastname = 'Wayne';
		$address = $model->getHomeAddress();
		$address->postal_code = '34-100';

		$duplicates = $model->getDuplicatesDataProvider();
		$this->tester->assertNotNull($duplicates);

		$this->tester->assertSame(1, $duplicates->getTotalCount());
		$this->tester->assertTrue($model->hasDuplicates());
		$this->tester->assertFalse($model->acceptDuplicates());

		$this->thenUnsuccessValidate();
		$this->thenSeeError('You must accept duplicates before create User.', 'acceptDuplicates');
		$this->model->acceptDuplicates = true;
		$this->thenSuccessSave();
	}

	public function testCorrectCreate(): void {
		$this->giveModel();
		$model = $this->getModel();
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
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->username = 'new_username';
		$model->getProfile()->lastname = 'lastname';
		$model->getProfile()->firstname = 'firstname';
		$this->thenSuccessSave();
		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, ['username' => 'new_username']);
		expect($user)->isInstanceOf(User::class);
		$this->tester->assertSame('firstname', $user->profile->firstname);
		$this->tester->assertSame('lastname', $user->profile->lastname);
	}

	public function testInvalidTrait(): void {
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [121212];
		$this->thenUnsuccessSave();
		$this->thenSeeError('Traits is invalid.', 'traits');
	}

	public function testAssignTraitToUser(): void {
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [UserTraitAssign::TRAIT_BAILIFF];
		$this->thenSuccessSave();
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
	}

	public function testAssignTraitsToUser(): void {
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		$model->traits = [UserTraitAssign::TRAIT_COMMISSION_REFUND, UserTraitAssign::TRAIT_BAILIFF];
		$this->thenSuccessSave();
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_BAILIFF, 'user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['trait_id' => UserTraitAssign::TRAIT_COMMISSION_REFUND, 'user_id' => $user->id]);
	}

	public function testAssignTraitAsEmptyArray(): void {
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$model->setModel($user);
		$model->traits = [];
		$this->thenSuccessSave();
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => 2]);
	}

	public function testAssignTraitAsEmptyString(): void {
		$this->giveModel();
		$model = $this->getModel();
		$user = $this->tester->grabFixture('user', 0);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$model->setModel($user);
		$model->traits = '';
		$this->thenSuccessSave();
		$this->tester->dontSeeRecord(UserTraitAssign::class, ['user_id' => $user->id]);
		$this->tester->seeRecord(UserTraitAssign::class, ['user_id' => 2]);
	}

	private function giveModel(array $config = []): void {
		$this->model = new UserForm($config);
	}

	public function getModel(): UserForm {
		return $this->model;
	}
}
