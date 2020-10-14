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

	public int $status = User::STATUS_INACTIVE;
	public string $username = '';
	public ?string $email = null;
	public string $password = '';

	public $roles = [];
	public $permissions = [];

	public bool $sendEmail = false;
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
			['email', 'default', 'value' => null],
			['status', 'integer'],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			[
				'roles', 'each',
				'rule' => [
					'in', 'range' => array_keys(static::getRolesNames()),
				],
			],
			[
				'permissions', 'each',
				'rule' => [
					'in', 'range' => array_keys(static::getPermissionsNames()),
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
			'permissions' => Yii::t('backend', 'Permissions'),
		];
	}

	public function setModel(User $model): void {
		$this->model = $model;
		$this->username = $model->username;
		$this->email = $model->email;
		$this->status = $model->status;
		$this->roles = $model->getRoles();
		$this->permissions = $model->getPermissions();
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
			$this->profile = $this->getModel()->profile;
		}
		return $this->profile;
	}

	public function getAddress(): Address {
		if (!$this->address) {
			$this->address = $this->getModel()->homeAddress ?: new Address();
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

		$this->applyAuth($model->id);

		if (!$this->updateProfile($model) || !$this->updateHomeAddress($model)) {
			return false;
		}

		if ($this->sendEmail &&
			$this->status === User::STATUS_INACTIVE
			&& $isNewRecord
			&& $this->email !== null
			&& !$this->hasErrors('email')) {
			return $this->sendEmail($model);
		}
		return true;
	}

	protected function applyAuth(int $id): void {
		Yii::$app->authManager->revokeAll($id);
		$this->assignRoles($id);
		$this->assignPermissions($id);
	}

	private function updateProfile(User $model): bool {
		$profile = $model->profile;
		$profile->lastname = $this->getProfile()->lastname;
		$profile->firstname = $this->getProfile()->firstname;
		$profile->phone = $this->getProfile()->phone;
		$profile->phone_2 = $this->getProfile()->phone_2;
		return $profile->save();
	}

	private function updateHomeAddress(User $model): bool {
		$address = $this->getAddress();
		if (!$address->save()) {
			return false;
		}

		$homeAddress = $model->addresses[UserAddress::TYPE_HOME] ?? new UserAddress(['type' => UserAddress::TYPE_HOME]);

		$homeAddress->user_id = $model->id;
		$homeAddress->address_id = $address->id;
		return $homeAddress->save();
	}

	private function assignRoles(int $userId): void {
		$auth = Yii::$app->authManager;
		foreach ((array) $this->roles as $roleName) {
			$role = $auth->getRole($roleName);
			if ($role) {
				$auth->assign($role, $userId);
			}
		}
	}

	private function assignPermissions(int $userId): void {
		$auth = Yii::$app->authManager;
		foreach ((array) $this->permissions as $permissionName) {
			$permission = $auth->getPermission($permissionName);
			if ($permission) {
				$auth->assign($permission, $userId);
			}
		}
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

	public static function getPermissionsNames(): array {
		return User::getPermissionsNames();
	}

	public static function getStatusNames(): array {
		return User::getStatusesNames();
	}
}
