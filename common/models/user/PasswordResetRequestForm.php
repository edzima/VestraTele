<?php

namespace common\models\user;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model {

	protected const USER_STATUSES = [
		User::STATUS_ACTIVE,
	];

	public static function forUser(User $model): bool {
		return !empty($model->email) && in_array($model->status, static::USER_STATUSES, true);
	}

	public $email;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			[
				'email', 'exist',
				'targetClass' => User::class,
				'filter' => ['status' => static::USER_STATUSES],
				'message' => 'There is no user with this email address.',
			],
		];
	}

	/**
	 * Sends an email with a link, for resetting the password.
	 *
	 * @return bool whether the email was send
	 */
	public function sendEmail(): bool {
		/* @var $user User */
		$user = User::findOne([
			'status' => static::USER_STATUSES,
			'email' => $this->email,
		]);

		if (!$user) {
			return false;
		}

		if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
			$user->generatePasswordResetToken();
			if (!$user->save()) {
				return false;
			}
		}

		return Yii::$app
			->mailer
			->compose(
				['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
				['user' => $user]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
			->setTo($this->email)
			->setSubject(
				Yii::t('common', 'Password reset for {appName}', [
					'appName' => Yii::$app->name,
				])
			)
			->send();
	}
}
