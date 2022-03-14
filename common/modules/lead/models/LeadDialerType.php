<?php

namespace common\modules\lead\models;

use common\models\user\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_dialer_type".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $user_id
 * @property int $type
 * @property int $did
 *
 * @property LeadDialer[] $leadDialers
 * @property User $user
 */
class LeadDialerType extends ActiveRecord {

	public const STATUS_ACTIVE = 1;
	public const STATUS_INACTIVE = 0;
	public const STATUS_DELETED = 2;

	public const TYPE_EXTENSION = 1;
	public const TYPE_QUEUE = 10;

	public static function getTypesNames(): array {
		return [
			static::TYPE_EXTENSION => Yii::t('lead', 'Extension'),
			static::TYPE_QUEUE => Yii::t('lead', 'Queue'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_dialer_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'status', 'user_id', 'type', 'did'], 'required'],
			[['status', 'user_id', 'did', 'type'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'status' => Yii::t('lead', 'Status'),
			'statusName' => Yii::t('lead', 'Status'),
			'user_id' => Yii::t('lead', 'User ID'),
			'type' => Yii::t('lead', 'Type'),
			'typeName' => Yii::t('lead', 'Type'),
			'did' => Yii::t('lead', 'DID'),
		];
	}

	/**
	 * Gets query for [[LeadDialers]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeadDialers() {
		return $this->hasMany(LeadDialer::class, ['type_id' => 'id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_ACTIVE => Yii::t('lead', 'Active'),
			static::STATUS_INACTIVE => Yii::t('lead', 'Inactive'),
			static::STATUS_DELETED => Yii::t('lead', 'Deleted'),
		];
	}

	public function isActive(): bool {
		return $this->status === static::STATUS_ACTIVE;
	}
}
