<?php

namespace common\models\user;

use common\models\Address;
use common\models\hierarchy\Hierarchy;
use common\models\issue\IssueCost;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueCostQuery;
use common\models\provision\Provision;
use common\models\provision\ProvisionQuery;
use common\models\user\query\UserQuery;
use common\modules\lead\models\LeadUserInterface;
use Yii;
use yii\base\InvalidConfigException;
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
 * @property string|null $email
 * @property integer $status
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $action_at
 * @property string $authKey
 * @property integer|null $boss
 * @property-write string $password
 * @property-read UserProfile $profile
 * @property-read UserProfile $userProfile
 * @property-read UserAddress[] $addresses
 * @property-read Address|null $homeAddress
 * @property-read Address|null $postalAddress
 * @property-read IssueUser[] $issueUsers
 * @property-read UserTraitAssign[] $traits
 * @property-read IssueCost[] $costs
 *
 */
class User extends ActiveRecord implements IdentityInterface, Hierarchy, LeadUserInterface {

	public const STATUS_INACTIVE = 0;
	public const STATUS_ACTIVE = 1;
	public const STATUS_BANNED = 2;
	public const STATUS_DELETED = 3;

	public const ROLE_DEFAULT = self::ROLE_USER;

	public const ROLE_ADMINISTRATOR = 'administrator';
	public const ROLE_MANAGER = 'manager';
	public const ROLE_USER = 'user';

	public const ROLE_RECCOMENDING = 'recommending';

	//workers
	public const ROLE_AGENT = 'agent';
	public const ROLE_CO_AGENT = 'co-agent';
	public const ROLE_BOOKKEEPER = 'book_keeper';
	public const ROLE_CUSTOMER_SERVICE = 'customer_service';
	public const ROLE_TELEMARKETER = 'telemarketer';
	public const ROLE_LAWYER = 'lawyer';
	public const ROLE_LAWYER_ASSISTANT = 'lawyer_assistant';

	public const ROLE_LAWYER_OFFICE = 'lawyer_office';

	public const ROLE_GUARDIAN = 'guardian';
	public const ROLE_VINDICATOR = 'vindicator';

	public const ROLE_ISSUE_FILE_MANAGER = 'issue-file-manager';

	public const PERMISSION_ARCHIVE = 'archive';
	public const PERMISSION_ARCHIVE_DEEP = 'archive.deep';
	public const PERMISSION_MESSAGE_TEMPLATE = 'message.template';
	public const PERMISSION_EXPORT = 'export';
	public const PERMISSION_ISSUE = 'issue';
	public const PERMISSION_HINT = 'hint';
	public const PERMISSION_LOGS = 'logs';
	public const PERMISSION_NEWS = 'news';
	public const PERMISSION_NEWS_MANAGER = 'news.manager';

	public const PERMISSION_NOTE = 'note';
	public const PERMISSION_NOTE_SELF = 'note.self';

	public const PERMISSION_SUMMON = 'summon';
	public const PERMISSION_COST = 'cost';

	public const PERMISSION_CALCULATION_TO_CREATE = 'calculation.to-create';
	public const PERMISSION_CALCULATION_UPDATE = 'calculation.update';
	public const PERMISSION_CALCULATION_PROBLEMS = 'calculation.problems';
	public const PERMISSION_CALCULATION_PAYS = 'calculation.pays';

	public const PERMISSION_PAY = 'pay';
	public const PERMISSION_PAY_UPDATE = 'pay.update';
	public const PERMISSION_PAY_PAID = 'pay.paid';
	public const PERMISSION_PAY_RECEIVED = 'pay.received';
	public const PERMISSION_PAYS_DELAYED = 'pays.delayed';
	public const PERMISSION_PAY_PART_PAYED = 'pay.part-payed';

	public const PERMISSION_PROVISION = 'provision';

	public const PERMISSION_WORKERS = 'workers';
	public const PERMISSION_WORKERS_HIERARCHY = 'workers.hierarchy';

	public const PERMISSION_LEAD = 'lead';
	public const PERMISSION_LEAD_MANAGER = 'lead.manager';
	public const PERMISSION_LEAD_DIALER = 'lead.dialer';
	public const PERMISSION_LEAD_DIALER_MANAGER = 'lead.dialer.manager';
	public const PERMISSION_LEAD_IMPORT = 'lead.import';
	public const PERMISSION_LEAD_UPDATE_MULTIPLE = 'lead.update-multiple';

	public const PERMISSION_LEAD_MARKET = 'lead.market';
	public const PERMISSION_LEAD_STATUS = 'lead.status';
	public const PERMISSION_LEAD_DUPLICATE = 'lead.duplicate';
	public const PERMISSION_LEAD_SMS_WELCOME = 'lead.sms.welcome';

	public const PERMISSION_CZATER = 'czater';
	public const PERMISSION_SMS = 'sms';
	public const PERMISSION_MULTIPLE_SMS = 'sms.multiple';
	public const PERMISSION_LEAD_DELETE = 'lead.delete';
	public const PERMISSION_USER_TRAITS = 'user.traits';
	public const PERMISSION_NOTE_UPDATE = 'note.update';
	public const PERMISSION_ISSUE_VISIBLE_NOT_SELF = 'issue.visible_not_self';

	private ?UserProfile $_profile = null;
	private static $ROLES_NAMES;
	private static $PERMISSIONS_NAMES;

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
		if (empty(trim($name))) {
			$name = $this->username;
		}
		return $name;
	}

	public function getPhone(): ?string {
		$profile = $this->profile;
		if (!empty($profile->phone)) {
			return $profile->phone;
		}
		if (!empty($profile->phone_2)) {
			return $profile->phone_2;
		}
		return null;
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
			'fullName' => Yii::t('common', 'Full name'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'action_at' => Yii::t('common', 'Last action at'),
			'boss' => Yii::t('common', 'Boss'),
		];
	}

	public function hasParent(): bool {
		return $this->getParentId() !== null;
	}

	public function getParentId(): ?int {
		return $this->boss;
	}

	public function getParentsIds(): array {
		if (!$this->hasParent()) {
			return [];
		}
		return Yii::$app->userHierarchy->getParentsIds($this->id);
	}

	public function getChildesIds(): array {
		return Yii::$app->userHierarchy->getChildesIds($this->id);
	}

	public function getAllChildesQuery(): UserQuery {
		return static::find()->where(['id' => $this->getAllChildesIds()]);
	}

	public function getAllParentsQuery(): ?UserQuery {
		if ($this->hasParent()) {
			return static::find()->where(['id' => $this->getParentsIds()]);
		}
		return null;
	}

	public function getAllChildesIds(): array {
		return Yii::$app
			->userHierarchy->getAllChildesIds($this->id);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssueCosts(): IssueCostQuery {
		return $this->hasMany(IssueCost::class, ['user_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getProvisions(): ProvisionQuery {
		return $this->hasMany(Provision::class, ['to_user_id' => 'id']);
	}

	public function getUserProfile(): ActiveQuery {
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
	}

	public function getProfile(): UserProfile {
		if ($this->_profile !== null) {
			return $this->_profile;
		}
		$profile = $this->userProfile;
		if ($profile === null) {
			$profile = new UserProfile(['user_id' => $this->id]);
		}
		$this->_profile = $profile;
		return $profile;
	}

	public function getHomeAddress(): ?Address {
		return $this->addresses[UserAddress::TYPE_HOME]->address ?? null;
	}

	public function getPostalAddress(): ?Address {
		return $this->addresses[UserAddress::TYPE_POSTAL]->address ?? null;
	}

	public function getTraits(): ActiveQuery {
		return $this->hasMany(UserTraitAssign::class, ['user_id' => 'id'])->indexBy('trait_id');
	}

	public function getTraitsNames(): string {
		$traits = $this->traits;
		if (empty($traits)) {
			return '';
		}
		return implode(", ", ArrayHelper::getColumn($traits, 'name'));
	}

	protected function getAddresses(): ActiveQuery {
		return $this->hasMany(UserAddress::class, ['user_id' => 'id'])->indexBy('type');
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

	public function getIssueUsers(): ActiveQuery {
		return $this->hasMany(IssueUser::class, ['user_id' => 'id']);
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

	public function getRoles(): array {
		return ArrayHelper::getColumn(
			Yii::$app->authManager->getRolesByUser($this->id),
			'name'
		);
	}

	public function getPermissions(): array {
		return ArrayHelper::getColumn(
			Yii::$app->authManager->getPermissionsByUser($this->id),
			'name'
		);
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
				$rolesI18n[$name] = Yii::t('rbac', $name);
			}
			static::$ROLES_NAMES = $rolesI18n;
		}
		return static::$ROLES_NAMES;
	}

	public static function getPermissionsNames(): array {
		if (empty(static::$PERMISSIONS_NAMES)) {
			$roles = Yii::$app->authManager->getPermissions();
			$rolesI18n = [];
			foreach ($roles as $role) {
				$name = $role->name;
				$rolesI18n[$name] = Yii::t('rbac', $name);
			}
			static::$PERMISSIONS_NAMES = $rolesI18n;
		}
		return static::$PERMISSIONS_NAMES;
	}

	/**
	 * @param array $names
	 * @param bool $common
	 * @return int[]
	 * @throws InvalidConfigException
	 */
	public static function getAssignmentIds(array $names, bool $common = true): array {
		return static::find()
			->select('id')
			->onlyAssignments($names, $common)
			->column();
	}

	/**
	 * Users names list indexed by ID
	 *
	 * @param int[] $ids
	 * @param bool $active
	 * @return string[]
	 */
	public static function getSelectList(array $ids, bool $active = true): array {
		$query = static::find()
			->joinWith('userProfile UP')
			->select(['id', 'username', 'firstname', 'lastname'])
			->andWhere(['id' => $ids])
			->orderBy('UP.lastname');
		if ($active) {
			$query->active();
		}

		$query->cache(60);
		return ArrayHelper::map(
			$query->all(), 'id', 'fullName');
	}

	/**
	 * @inheritdoc
	 */
	public static function find(): UserQuery {
		return new UserQuery(static::class);
	}

	public function getEmail(): string {
		return $this->email;
	}
}
