<?php

namespace backend\modules\user\models;

use common\models\Address;
use common\models\user\User;
use common\models\user\UserAddress;
use common\models\user\UserProfile;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

/**
 * Create user form.
 */
class UserForm extends Model {

	public const SCENARIO_CREATE = 'create';

	public int $status = User::STATUS_ACTIVE;
	public string $username = '';
	public ?string $email = null;
	public string $password = '';

	public $roles = [];

	public bool $isEmailRequired = true;

	private $model;
	private $profile;
	private $address;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return array_merge([
			['username', 'trim'],
			['username', 'required'],
			['username', 'match', 'pattern' => '#^[\w_\-\.]+$$#i'],
			[
				'username', 'unique',
				'targetClass' => User::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			['username', 'string', 'min' => 2, 'max' => 32],
			['password', 'required', 'on' => static::SCENARIO_CREATE],
			['password', 'string', 'min' => 6, 'max' => 32],
			['email', 'trim'],
			['email', 'email'],
			[
				'email', 'unique',
				'targetClass' => User::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},

			],
			['email', 'string', 'max' => 255],
			['status', 'integer'],
			['status', 'in', 'range' => array_keys(User::getStatusesNames())],
			[
				'roles', 'each',
				'rule' => [
					'in', 'range' => array_keys(static::getRolesNames()),
				],
			],
		], $this->emailRules());
	}

	protected function emailRules(): array {
		if ($this->isEmailRequired) {
			return [
				['email', 'required'],
			];
		}
		return [
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'username' => Yii::t('backend', 'Login'),
			'email' => Yii::t('backend', 'Email'),
			'password' => Yii::t('backend', 'Password'),
			'status' => Yii::t('backend', 'Status'),
			'roles' => Yii::t('backend', 'Roles'),
		];
	}

	public function setModel(User $model): void {
		$this->model = $model;
		$this->username = $model->username;
		$this->email = $model->email;
		$this->status = $model->status;
		$this->roles = $model->getRoles();
	}

	public function getModel(): User {
		if (!$this->model) {
			$this->scenario = static::SCENARIO_CREATE;
			$this->model = $this->createModel();
		}
		return $this->model;
	}

	protected function createModel(): User {
		return new User();
	}

	public function getProfile(): UserProfile {
		if (!$this->profile) {
			$this->profile = $this->getModel()->profile ?? new UserProfile();
		}
		return $this->profile;
	}

	public function getAddress(): Address {
		if (!$this->address) {
			$this->address = $this->getModel()->homeAddress ?? new Address();
		}
		return $this->address;
	}

	public function load($data, $formName = null): bool {
		return parent::load($data)
			&& $this->getProfile()->load($data)
			&& $this->getAddress()->load($data);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getProfile()->validate($attributeNames, $clearErrors)
			&& $this->getAddress()->validate($attributeNames, $clearErrors);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$this->beforeSaveModel($model);

		$isNewRecord = $model->isNewRecord;
		if (!$model->save()) {
			return false;
		}

		$model->setRoles($this->roles);

		$profile = $this->getProfile();
		$profile->user_id = $model->id;
		$profile->isNewRecord = $model->profile->isNewRecord;
		if (!$profile->save()) {
			return false;
		}

		$address = $this->getAddress();
		if ($address->save()) {
			$homeAddress = $model->addresses[UserAddress::TYPE_HOME] ?? new UserAddress(['type' => UserAddress::TYPE_HOME]);
			$homeAddress->user_id = $model->id;
			$homeAddress->address_id = $address->id;
			$homeAddress->save();
		}
		if ($this->status === User::STATUS_INACTIVE
			&& $isNewRecord
			&& $this->email !== null
			&& !$this->hasErrors('email')) {
			return $this->sendEmail($model);
		}
		return true;
	}

	protected function beforeSaveModel(User $model): void {
		$model->username = $this->username;
		$model->status = $this->status;
		if (!$this->hasErrors('email')) {
			$model->email = $this->email;
		}
		if ($this->scenario === static::SCENARIO_CREATE) {
			$model->generateAuthKey();
			$model->generateEmailVerificationToken();
		}

		if ($this->password) {
			$model->setPassword($this->password);
		}
	}

	/**
	 * Sends confirmation email to user
	 *
	 * @param User $user user model to with email should be send
	 * @return bool whether the email was sent
	 */
	protected function sendEmail(User $user): bool {
		return Yii::$app
			->mailer
			->compose(
				['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
				['user' => $user]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
			->setTo($user->email)
			->setSubject('Account registration at ' . Yii::$app->name)
			->send();
	}

	public static function getRolesNames(): array {
		return User::getRolesNames();
	}

	public static function getStatusNames(): array {
		return User::getStatusesNames();
	}
}
