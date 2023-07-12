<?php

namespace frontend\models;

use Yii;
use common\models\user\User;
use yii\base\Model;

class ResendVerificationEmailForm extends Model {

	/**
	 * @var string
	 */
	public $email;

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			[
				'email', 'exist',
				'targetClass' => User::class,
				'filter' => ['status' => User::STATUS_INACTIVE],
				'message' => 'There is no user with this email address.',
			],
		];
	}

	/**
	 * Sends confirmation email to user
	 *
	 * @return bool whether the email was sent
	 */
	public function sendEmail() {
		$user = User::findOne([
			'email' => $this->email,
			'status' => User::STATUS_INACTIVE,
		]);

		if ($user === null) {
			return false;
		}

		return Yii::$app
			->mailer
			->compose(
				['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
				['user' => $user]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
			->setTo($this->email)
			->setSubject(
				Yii::t('common', 'Account registration at {appName}', [
					'appName' => Yii::$app->name,
				])
			)
			->send();
	}
}
