<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\user\Customer;
use common\models\user\query\UserQuery;
use common\models\user\User;
use common\models\user\Worker;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_user".
 *
 * @property int $user_id
 * @property int $issue_id
 * @property string $type
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueUser extends ActiveRecord {

	public const TYPE_LAWYER = Worker::ROLE_LAWYER;
	public const TYPE_AGENT = Worker::ROLE_AGENT;
	public const TYPE_TELEMARKETER = Worker::ROLE_TELEMARKETER;
	public const TYPE_CUSTOMER = Customer::ROLE_CUSTOMER;
	public const TYPE_VICTIM = Customer::ROLE_VICTIM;
	public const TYPE_SHAREHOLDER = Customer::ROLE_SHAREHOLDER;
	public const TYPE_MINOR = Customer::ROLE_MINOR;
	public const TYPE_DIED = Customer::ROLE_DIED;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'issue_user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'issue_id', 'type'], 'required'],
			[['user_id', 'issue_id'], 'integer'],
			[['type'], 'string', 'max' => 255],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['user_id', 'issue_id'], 'unique', 'targetAttribute' => ['user_id', 'issue_id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
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
			'issue_id' => 'Issue ID',
			'type' => 'Type',
		];
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function getTypesNames(): array {

		return [
			static::TYPE_AGENT => User::getRolesNames()[static::TYPE_AGENT],
			static::TYPE_CUSTOMER => User::getRolesNames()[static::TYPE_CUSTOMER],
			static::TYPE_LAWYER => User::getRolesNames()[static::TYPE_LAWYER],
			static::TYPE_TELEMARKETER => User::getRolesNames()[static::TYPE_TELEMARKETER],
			static::TYPE_VICTIM => User::getRolesNames()[static::TYPE_VICTIM],
			static::TYPE_MINOR => User::getRolesNames()[static::TYPE_MINOR],
			static::TYPE_SHAREHOLDER => User::getRolesNames()[static::TYPE_SHAREHOLDER],
			static::TYPE_DIED => User::getRolesNames()[static::TYPE_DIED],
		];
	}

	public static function findByTypes(array $types): ActiveQuery {
		return static::find()->where(['type' => $types]);
	}

	/**
	 * @inheritdoc
	 * @return IssueUserQuery the active query used by this AR class.
	 */
	public static function find(): IssueUserQuery {
		return new IssueUserQuery(static::class);
	}

}
