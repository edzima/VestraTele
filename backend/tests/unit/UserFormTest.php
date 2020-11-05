<?php

namespace backend\tests\unit;

use backend\modules\user\models\UserForm;
use common\fixtures\UserFixture;
use common\models\user\User;

class UserFormTest extends Unit {

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		]);
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

		$this->tester->assertTrue($model->save());

		/** @var User $user */
		$user = $this->tester->grabRecord(User::class, [
			'username' => 'some_username',
			'email' => 'test@email.com',
			'status' => User::STATUS_INACTIVE,
		]);
		expect($user)->isInstanceOf(User::class);
		$this->assertSame('some_firstname', $user->profile->firstname);
		$this->assertSame('some_lastname', $user->profile->lastname);

		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('test@email.com');
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
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

}
