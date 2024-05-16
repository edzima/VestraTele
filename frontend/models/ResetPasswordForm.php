<?php

namespace frontend\models;

use common\models\user\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model {

	public $password;

	/**
	 * @var User
	 */
	private $_user;

	/**
	 * Creates a form model given a token.
	 *
	 * @param string $token
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 * @throws InvalidArgumentException if token is empty or not valid
	 */
	public function __construct($token, $config = []) {
		if (empty($token) || !is_string($token)) {
			throw new InvalidArgumentException('Password reset token cannot be blank.');
		}
		$this->_user = User::findByPasswordResetToken($token);
		if (!$this->_user) {
			throw new InvalidArgumentException('Wrong password reset token.');
		}
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'password' => Yii::t('common', 'Password'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			['password', 'required'],
			['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
		];
	}

	/**
	 * Resets password.
	 *
	 * @return bool if password was reset.
	 */
	public function resetPassword(): bool {
		if (!$this->validate()) {
			return false;
		}
		$user = $this->_user;
		$user->setPassword($this->password);
		$user->removePasswordResetToken();

		return $user->save(false);
	}
}
