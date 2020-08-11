<?php

namespace common\models;
use app\models\UserAddress;
use Closure;
use common\models\address\Address;
use developeruz\db_rbac\interfaces\UserRbacInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use common\models\query\UserQuery;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_hash
 * @property string $email
 * @property integer $status
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $action_at
 * @property string $typ_work
 * @property string $typWork
 * @property string $password
 * @property string $authKey
 * @property integer $boss
 * @property UserProfile $userProfile
 * @property User $parent
 * @property-read Address[] A
 */
class User extends ActiveRecord implements IdentityInterface, UserRbacInterface {

	public const STATUS_INACTIVE = 0;
	public const STATUS_ACTIVE = 1;
	public const STATUS_BANNED = 2;
	public const STATUS_DELETED = 3;

	public const ROLE_ADMINISTRATOR = 'administrator';
	public const ROLE_MANAGER = 'manager';
	public const ROLE_USER = 'user';

	public const ROLE_ARCHIVE = 'archive';
	public const ROLE_ISSUE = 'issue';
	public const ROLE_LOGS = 'logs';
	public const ROLE_MEET = 'meet';
	public const ROLE_NEWS = 'news';
	public const ROLE_NOTE = 'note';

	public const ROLE_AGENT = 'agent';
	public const ROLE_BOOKKEEPER = 'book_keeper';
	public const ROLE_BOOKKEEPER_DELAYED = 'book_keeper_delayed';
	public const ROLE_CUSTOMER_SERVICE = 'customer_service';
	public const ROLE_TELEMARKETER = 'telemarketer';
	public const ROLE_LAWYER = 'lawyer';

	public const ROLE_CLIENT = 'client';
	public const ROLE_VICTIM = 'victim';

	private $selfTree;
	private static $BOSS_MAP = [];
	private static $TREE = [];
	private static $USER_NAMES = [];

	const EVENT_AFTER_SIGNUP = 'afterSignup';

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
		];
	}

	public function beforeSave($insert) {
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
		if ($this->userProfile !== null) {
			if ($this->userProfile->lastname !== null) {
				$name .= $this->userProfile->lastname;
			}
			if ($this->userProfile->firstname !== null) {
				$name .= ' ' . $this->userProfile->firstname;
			}
		}
		if ($name === '') {
			$name = $this->username;
		}
		return $name;
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['username', 'email'], 'unique'],
			['username', 'filter', 'filter' => '\yii\helpers\Html::encode'],
			['status', 'default', 'value' => self::STATUS_INACTIVE],
			['status', 'in', 'range' => array_keys(self::statuses())],
			['ip', 'ip'],
			['boss', 'number'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'username' => Yii::t('common', 'Username'),
			'email' => Yii::t('common', 'Email'),
			'status' => Yii::t('common', 'Status'),
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'action_at' => Yii::t('common', 'Last action at'),
			'boss' => Yii::t('common', 'Boss'),
			'fullName' => 'Imie i nazwisko',
			//'typ_work' => 'rodzaj pracownika'
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserProfile() {
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
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
	public static function findByUsername($username) {
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->getPrimaryKey();
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->auth_key;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUserAddress() {
		return $this->hasMany(UserAddress::class, ['user_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 */
	public function getTypWork() {

		return $this->typ_work;
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
	public function validatePassword($password) {
		return Yii::$app->security->validatePassword($password, $this->password_hash);
	}

	/**
	 * Generates password hash from password and sets it to the model.
	 *
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates "remember me" authentication key.
	 */
	public function generateAuthKey() {
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates new access token.
	 */
	public function generateAccessToken() {
		$this->access_token = Yii::$app->security->generateRandomString();
	}

	/**
	 * Removes access token.
	 */
	public function removeAccessToken() {
		$this->access_token = null;
	}

	/**
	 * Returns user statuses list
	 *
	 * @param mixed $status
	 * @return array|mixed
	 */
	public static function statuses($status = null) {
		$statuses = [
			self::STATUS_INACTIVE => Yii::t('common', 'Inactive'),
			self::STATUS_ACTIVE => Yii::t('common', 'Active'),
			self::STATUS_BANNED => Yii::t('common', 'Banned'),
			self::STATUS_DELETED => Yii::t('common', 'Deleted'),
		];

		if ($status === null) {
			return $statuses;
		}

		return $statuses[$status];
	}

	/**
	 * @return array
	 */
	public static function roleI18n() {
		$roles = Yii::$app->authManager->getRoles();
		$rolesI18n = [];

		foreach ($roles as $role) {
			$name = $role->name;
			$rolesI18n[$name] = Yii::t('common', $name);
		}
		return $rolesI18n;
	}

	public function hasParent(): bool {
		return $this->boss !== null;
	}

	public function getParent() {
		return $this->hasOne(static::class, ['boss' => 'id']);
	}

	/**
	 * Creates user profile and application event.
	 *
	 * @param array $profileData
	 * @throws \Exception
	 */
	public function afterSignup(array $profileData = []) {
		$profile = new UserProfile();
		$profile->load($profileData, '');
		$this->link('userProfile', $profile);

		// Default role
		$auth = Yii::$app->authManager;
		$auth->assign($auth->getRole(self::ROLE_USER), $this->getId());
	}

	/**
	 * @inheritdoc
	 * @return \common\models\query\UserQuery the active query used by this AR class.
	 */
	public static function find() {
		return new UserQuery(get_called_class());
	}

	public static function getSelectList(array $roles = [], bool $commonRoles = true, ?Closure $beforeAll = null): array {
		$query = static::find()
			->joinWith('userProfile')
			->with('userProfile')
			->active()
			->orderBy('user_profile.lastname');
		if (!empty($roles)) {
			$query->onlyByRole($roles, $commonRoles);
		}
		if ($beforeAll instanceof Closure) {
			$beforeAll($query);
		}
		$query->cache(60);

		return ArrayHelper::map(
			$query->all(), 'id', 'fullName');
	}

	public function getParents(): array {
		return $this->getParentsQuery()->all();
	}

	public function getParentsQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getParentsIds()]);
	}

	public function getParentsIds(): array {
		if (!$this->hasParent()) {
			return [];
		}
		return static::findParents($this->id);
	}

	private static function findParents(int $userId): array {
		$ids = [];
		while (($userId = static::getBossId($userId)) !== null) {
			$ids[] = $userId;
		}
		return $ids;
	}

	private static function getBossId(int $userId): ?int {
		return static::getBossesIdsMap()[$userId] ?? null;
	}

	/**
	 * @return static[]
	 */
	public function getChildes(): array {
		return $this->getChildesQuery()->all();
	}

	public function getChildesQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getChildesIds()]);
	}

	public function getChildesIds(): array {
		$ids = [];
		foreach (static::getBossesIdsMap() as $id => $boss) {
			if ($boss === $this->id) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	public function getAllChildes(): array {
		return $this->getAllChildesQuery()->all();
	}

	public function getAllChildesQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getAllChildesIds()]);
	}

	public function getAllChildesIds(): array {
		$selfTree = $this->getSelfTree();
		$ids = [];
		array_walk_recursive($selfTree, static function ($item, $key) use (&$ids) {
			if ($key === 'id') {
				$ids[] = $item;
			}
		});
		return $ids;
	}

	private static function getBossesIdsMap(): array {
		if (empty(static::$BOSS_MAP)) {
			static::$BOSS_MAP = ArrayHelper::map(static::find()
				->select('id,boss')
				->onlyWithBoss()
				->active()
				->asArray()
				->all(), 'id', 'boss');
		}
		return static::$BOSS_MAP;
	}

	public function getSelfTree(): array {
		if (empty($this->selfTree)) {
			$this->selfTree = ArrayHelper::filter(static::getTree(), [$this->id]);
		}
		return $this->selfTree;
	}

	public static function getTree(): array {
		if (empty(static::$TREE)) {
			$boss = static::find()
				->select('id,boss')
				->onlyWithBoss()
				->active()
				->asArray()
				->all();
			static::$TREE = static::buildTree($boss, 'boss', 'id');
		}
		return static::$TREE;
	}

	private static function buildTree($items, string $parentId, string $id) {
		$childs = [];
		foreach ($items as &$item) {
			$childs[$item[$parentId]][] = &$item;
		}
		unset($item);
		foreach ($items as &$item) {
			if (isset($childs[$item[$id]])) {
				$item['childs'] = $childs[$item[$id]];
			}
		}
		return $childs;
	}

	public static function userName(int $id): string {
		return static::userNames()[$id];
	}

	private static function userNames(): array {
		if (empty(static::$USER_NAMES)) {
			static::$USER_NAMES = static::find()
				->select('username')
				->active()
				->asArray()
				->indexBy('id')
				->column();
		}
		return static::$USER_NAMES;
	}

	public function getUserName() {
		return $this->getFullName();
	}
}
