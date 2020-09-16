<?php

namespace frontend\modules\account\models;

use common\models\user\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form.
 */
class ResetPasswordForm extends Model {

	public $password;
	public $password_confirm;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * Creates a form model given a token.
	 *
	 * @param string $token
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 * @throws \yii\base\InvalidArgumentException if token is empty or not valid
	 */
	public function __construct($token, $config = []) {
		if (empty($token) || !is_string($token)) {
			throw new InvalidArgumentException(Yii::t('frontend', 'Password reset token cannot be blank.'));
		}
		$this->user = User::findOne([
			'access_token' => $token,
			'status' => User::STATUS_ACTIVE,
		]);
		if (!$this->user) {
			throw new InvalidArgumentException(Yii::t('frontend', 'Wrong password reset token.'));
		}
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			['password', 'required'],
			['password', 'string', 'min' => 6, 'max' => 32],

			['password_confirm', 'required'],
			['password_confirm', 'string', 'min' => 6, 'max' => 32],
			['password_confirm', 'compare', 'compareAttribute' => 'password'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'password' => Yii::t('frontend', 'Password'),
			'password_confirm' => Yii::t('frontend', 'Confirm password'),
		];
	}

	/**
	 * Resets password.
	 *
	 * @return bool if password was reset.
	 */
	public function resetPassword(): bool {
		$user = $this->user;
		$user->setPassword($this->password);
		$user->removeAccessToken();

		return $user->save(false);
	}
}
