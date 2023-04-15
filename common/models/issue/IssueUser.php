<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\user\Customer;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue_user".
 *
 * @property int $user_id
 * @property int $issue_id
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueUser extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const TYPE_LAWYER = Worker::ROLE_LAWYER;
	public const TYPE_LAWYER_OFFICE = Worker::ROLE_LAWYER_OFFICE;
	public const TYPE_LAWYER_ASSISTANT = Worker::ROLE_LAWYER_ASSISTANT;
	public const TYPE_AGENT = Worker::ROLE_AGENT;
	public const TYPE_CO_AGENT = Worker::ROLE_CO_AGENT;
	public const TYPE_TELEMARKETER = Worker::ROLE_TELEMARKETER;
	public const TYPE_CUSTOMER = Customer::ROLE_CUSTOMER;
	public const TYPE_VICTIM = Customer::ROLE_VICTIM;
	public const TYPE_SHAREHOLDER = Customer::ROLE_SHAREHOLDER;
	public const TYPE_HANDICAPPED = Customer::ROLE_HANDICAPPED;
	public const TYPE_RECOMMENDING = User::ROLE_RECCOMENDING;
	public const TYPE_GUARDIAN = User::ROLE_GUARDIAN;
	public const TYPE_VINDICATOR = User::ROLE_VINDICATOR;

	public const TYPES_WORKERS = [
		self::TYPE_LAWYER,
		self::TYPE_LAWYER_OFFICE,
		self::TYPE_LAWYER_ASSISTANT,
		self::TYPE_AGENT,
		self::TYPE_CO_AGENT,
		self::TYPE_TELEMARKETER,
		self::TYPE_VINDICATOR,
	];

	public const TYPES_CUSTOMERS = [
		self::TYPE_CUSTOMER,
		self::TYPE_HANDICAPPED,
		self::TYPE_VICTIM,
		self::TYPE_SHAREHOLDER,
		self::TYPE_RECOMMENDING,
		self::TYPE_GUARDIAN,
	];

	public const TYPES_ARCHIVE_ACCESS = [
		self::TYPE_AGENT,
		self::TYPE_CO_AGENT,
		self::TYPE_TELEMARKETER,
		self::TYPE_CUSTOMER,
		self::TYPE_RECOMMENDING,
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'issue_id', 'type'], 'required'],
			[['user_id', 'issue_id'], 'integer'],
			[['type'], 'string', 'max' => 255],
			[['created_at', 'updated_at'], 'safe'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['user_id', 'issue_id'], 'unique', 'targetAttribute' => ['user_id', 'issue_id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getTypeWithUser(): string {
		return $this->getTypeName() . ' - ' . $this->user->getFullName();
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => 'User ID',
			'issue_id' => Yii::t('common', 'Issue'),
			'type' => Yii::t('common', 'Type'),
		];
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function isWorkerType(): bool {
		return in_array($this->type, static::TYPES_WORKERS);
	}

	public function isCustomerType(): bool {
		return in_array($this->type, static::TYPES_CUSTOMERS);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_CUSTOMER => User::getRolesNames()[static::TYPE_CUSTOMER],
			static::TYPE_GUARDIAN => User::getRolesNames()[static::TYPE_GUARDIAN],
			static::TYPE_AGENT => User::getRolesNames()[static::TYPE_AGENT],
			static::TYPE_CO_AGENT => User::getRolesNames()[static::TYPE_CO_AGENT],
			static::TYPE_LAWYER => User::getRolesNames()[static::TYPE_LAWYER],
			static::TYPE_LAWYER_ASSISTANT => User::getRolesNames()[static::TYPE_LAWYER_ASSISTANT],
			static::TYPE_LAWYER_OFFICE => User::getRolesNames()[static::TYPE_LAWYER_OFFICE],
			static::TYPE_TELEMARKETER => User::getRolesNames()[static::TYPE_TELEMARKETER],
			static::TYPE_VINDICATOR => User::getRolesNames()[static::TYPE_VINDICATOR],
			static::TYPE_VICTIM => User::getRolesNames()[static::TYPE_VICTIM],
			static::TYPE_SHAREHOLDER => User::getRolesNames()[static::TYPE_SHAREHOLDER],
			static::TYPE_HANDICAPPED => User::getRolesNames()[static::TYPE_HANDICAPPED],
			static::TYPE_RECOMMENDING => User::getRolesNames()[static::TYPE_RECOMMENDING],
		];
	}

	public static function userIds(string $type): array {
		return static::find()
			->select('user_id')
			->distinct()
			->withType($type)
			->column();
	}

	/**
	 * @inheritdoc
	 * @return IssueUserQuery the active query used by this AR class.
	 */
	public static function find(): IssueUserQuery {
		return new IssueUserQuery(static::class);
	}

}
