<?php

namespace common\models\user;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model {

	public $usernameOrEmail;
	public $password;
	public $rememberMe = true;

	private $_user;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			// username and password are both required
			[['usernameOrEmail', 'password'], 'required'],
			// rememberMe must be a boolean value
			['rememberMe', 'boolean'],
			// password is validated by validatePassword()
			['password', 'validatePassword'],
		];
	}

	public function attributeLabels() {
		return [
			'usernameOrEmail' => Yii::t('common', 'Username / Email'),
			'password' => Yii::t('frontend', 'Password'),
			'rememberMe' => Yii::t('frontend', 'Remember Me'),
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validatePassword($attribute, $params) {
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user || !$user->validatePassword($this->password)) {
				$this->addError($attribute,
					Yii::t('common', 'Incorrect username or password.')
				);
			}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return bool whether the user is logged in successfully
	 */
	public function login(): bool {
		if ($this->validate()) {
			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
		}

		return false;
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	protected function getUser(): ?User {
		if ($this->_user === null) {
			$this->_user = User::find()
				->active()
				->andWhere([
					'or', [
						'email' => $this->usernameOrEmail,
					],
					[
						'username' => $this->usernameOrEmail,
					],
				])
				->one();
		}

		return $this->_user;
	}
}
