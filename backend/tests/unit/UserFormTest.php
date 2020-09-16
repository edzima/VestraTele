<?php

namespace backend\tests\unit;

use backend\modules\user\models\UserForm;
use common\fixtures\UserFixture;
use common\models\user\User;

class UserFormTest extends \Codeception\Test\Unit {

	/**
	 * @var \frontend\tests\UnitTester
	 */
	protected $tester;

	public function _before() {
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		]);
	}

	public function testCorrectCreate() {
		$model = new UserForm();

		$loaded = $model->load([
			'UserForm' => [
				'username' => 'some_username',
				'email' => 'some_email@example.com',
				'password' => 'some_password',
			],
			'UserProfile' => [
				'firstname' => 'some_firstname',
				'lastname' => 'some_lastname',
			],
		]);

		expect($loaded)->true();
		$user = $model->save();
		expect($user)->true();

		/** @var User $user */
		$user = $this->tester->grabRecord('common\models\user\User', [
			'username' => 'some_username',
			'email' => 'some_email@example.com',
			'status' => User::STATUS_INACTIVE,
		]);
		expect($user)->isInstanceOf(User::class);
		$this->assertSame('some_firstname', $user->profile->firstname);
		$this->assertSame('some_lastname', $user->profile->lastname);

		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('some_email@example.com');
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
		//l	expect($mail->toString())->stringContainsString($user->verification_token);
	}

	public function testUpdate(): void {
		$model = new UserForm();
		$user = $this->tester->grabFixture('user', 0);
		$model->setModel($user);
		expect($model->load([
			'UserForm' => [
				'username' => 'updated_username',
			],
		]))->true();
	}

}
