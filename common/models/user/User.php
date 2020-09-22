<?php

namespace common\models\user;

use common\models\Address;
use common\models\user\query\UserQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property integer $status
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $action_at
 * @property string $typ_work
 * @property string $typWork
 * @property string $authKey
 * @property integer|null $boss
 * @property-read UserProfile $profile
 * @property-read UserAddress[] $addresses
 * @property-read Address|null $homeAddress
 * @property string $password write-only password
 *
 */
class User extends ActiveRecord implements IdentityInterface {

	public const STATUS_INACTIVE = 0;
	public const STATUS_ACTIVE = 1;
	public const STATUS_BANNED = 2;
	public const STATUS_DELETED = 3;

	public const PERMISSION_SUMMON = 'summon';

	public const ROLE_DEFAULT = self::ROLE_USER;

	public const ROLE_ADMINISTRATOR = 'administrator';
	public const ROLE_MANAGER = 'manager';
	public const ROLE_USER = 'user';


	public const ROLE_AGENT = 'agent';
	public const ROLE_BOOKKEEPER = 'book_keeper';
	public const ROLE_CUSTOMER_SERVICE = 'customer_service';
	public const ROLE_TELEMARKETER = 'telemarketer';
	public const ROLE_LAWYER = 'lawyer';

	public const PERMISSION_ARCHIVE = 'archive';
	public const PERMISSION_ISSUE = 'issue';
	public const PERMISSION_LOGS = 'logs';
	public const PERMISSION_MEET = 'meet';
	public const PERMISSION_NEWS = 'news';
	public const PERMISSION_NOTE = 'note';
	public const PERMISSION_PAYS_DELAYED = 'pays.delayed';

	//customers
	public const ROLE_CLIENT = 'client';
	public const ROLE_VICTIM = 'victim';

	public const WORKERS_ROLES = [
		self::ROLE_AGENT,
		self::ROLE_TELEMARKETER,
		self::ROLE_BOOKKEEPER,
		self::ROLE_CUSTOMER_SERVICE,
		self::ROLE_LAWYER,
	];

	public const CUSTOMERS_ROLES = [
		self::ROLE_CLIENT,
		self::ROLE_VICTIM,
	];

	const EVENT_AFTER_SIGNUP = 'afterSignup';

	private static $ROLES_NAMES;
	private ?array $roles = null;

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	public function beforeSave($insert) {
		// @todo add test for this
		if ($this->boss === $this->id) {
			$this->boss = null;
		}
		return parent::beforeSave($insert);
	}

	public function __toString(): string {
		return $this->getFullName();
	}

	public function getFullName(): string {
		$name = '';
		if ($this->profile !== null) {
			if ($this->profile->lastname !== null) {
				$name .= $this->profile->lastname;
			}
			if ($this->profile->firstname !== null) {
				$name .= ' ' . $this->profile->firstname;
			}
		}
		if (trim($name) === '') {
			$name = $this->username;
		}
		return $name;
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['username', 'email'], 'unique'],
			['username', 'filter', 'filter' => '\yii\helpers\Html::encode'],
			['status', 'default', 'value' => self::STATUS_INACTIVE],
			['status', 'in', 'range' => array_keys(self::getStatusesNames())],
			['ip', 'ip'],
			['boss', 'number'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'username' => Yii::t('common', 'Username'),
			'email' => Yii::t('common', 'Email'),
			'status' => Yii::t('common', 'Status'),
			'statusName' => Yii::t('common', 'Status'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'action_at' => Yii::t('common', 'Last action at'),
			'boss' => Yii::t('common', 'Boss'),

		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	protected function getUserProfile() {
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
	}

	public function getProfile(): UserProfile {
		return $this->userProfile ?: new UserProfile(['user_id' => $this->id]);
	}

	protected function getAddresses(): ActiveQuery {
		return $this->hasMany(UserAddress::class, ['user_id' => 'id'])->indexBy('type');
	}

	public function getHomeAddress(): ?Address {
		return $this->addresses[UserAddress::TYPE_HOME]->address ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id) {
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * Finds user by username.
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername(string $username): ?self {
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * Finds user by password reset token
	 *
	 * @param string $token password reset token
	 * @return static|null
	 */
	public static function findByPasswordResetToken(string $token) {
		if (!static::isPasswordResetTokenValid($token)) {
			return null;
		}

		return static::findOne([
			'password_reset_token' => $token,
			'status' => self::STATUS_ACTIVE,
		]);
	}

	/**
	 * Finds user by verification email token
	 *
	 * @param string $token verify email token
	 * @return static|null
	 */
	public static function findByVerificationToken($token) {
		return static::findOne([
			'verification_token' => $token,
			'status' => self::STATUS_INACTIVE,
		]);
	}

	/**
	 * Finds out if password reset token is valid
	 *
	 * @param string $token password reset token
	 * @return bool
	 */
	public static function isPasswordResetTokenValid($token) {
		if (empty($token)) {
			return false;
		}

		$timestamp = (int) substr($token, strrpos($token, '_') + 1);
		$expire = Yii::$app->params['user.passwordResetTokenExpire'];
		return $timestamp + $expire >= time();
	}

	/**
	 * @inheritdoc
	 */
	public function getId(): int {
		return $this->getPrimaryKey();
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->auth_key;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey) {
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * Validates password.
	 *
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword(string $password) {
		return Yii::$app->security->validatePassword($password, $this->password_hash);
	}

	/**
	 * Generates password hash from password and sets it to the model.
	 *
	 * @param string $password
	 */
	public function setPassword(string $password): void {
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates "remember me" authentication key.
	 */
	public function generateAuthKey(): void {
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates new access token.
	 */
	public function generateAccessToken(): void {
		$this->access_token = Yii::$app->security->generateRandomString();
	}

	/**
	 * Removes access token.
	 */
	public function removeAccessToken(): void {
		$this->access_token = null;
	}

	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken(): void {
		$this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
	}

	/**
	 * Generates new token for email verification
	 */
	public function generateEmailVerificationToken(): void {
		$this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
	}

	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken(): void {
		$this->password_reset_token = null;
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function setRoles(array $roles): void {
		$auth = Yii::$app->authManager;
		$auth->revokeAll($this->id);
		if (empty($roles)) {
			$roles = [static::ROLE_DEFAULT];
		}
		foreach ($roles as $roleName) {
			$role = $auth->getRole($roleName);
			if ($role) {
				$auth->assign($role, $this->id);
			}
		}
	}

	public function getRoles(bool $refresh = false): array {
		if ($this->roles === null || $refresh) {
			$this->roles = ArrayHelper::getColumn(
				Yii::$app->authManager->getRolesByUser($this->id),
				'name'
			);
		}
		return $this->roles;
	}

	/**
	 * Returns user statuses list
	 *
	 * @return string[]
	 */
	public static function getStatusesNames(): array {
		return [
			self::STATUS_INACTIVE => Yii::t('common', 'Inactive'),
			self::STATUS_ACTIVE => Yii::t('common', 'Active'),
			self::STATUS_BANNED => Yii::t('common', 'Banned'),
			self::STATUS_DELETED => Yii::t('common', 'Deleted'),
		];
	}

	public static function getRolesNames(): array {
		if (empty(static::$ROLES_NAMES)) {
			$roles = Yii::$app->authManager->getRoles();
			$rolesI18n = [];
			foreach ($roles as $role) {
				$name = $role->name;
				$rolesI18n[$name] = Yii::t('common', $name);
			}
			static::$ROLES_NAMES = $rolesI18n;
		}
		return static::$ROLES_NAMES;
	}

	/**
	 * @inheritdoc
	 */
	public static function find(): UserQuery {
		return new UserQuery(get_called_class());
	}

}
