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
 *
 * @property LeadDialer[] $leadDialers
 * @property User $user
 */
class LeadDialerType extends ActiveRecord {

	public const STATUS_ACTIVE = 1;
	public const STATUS_INACTIVE = 0;
	public const STATUS_DELETED = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%lead_dialer_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'status', 'user_id'], 'required'],
			[['status', 'user_id'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'status' => Yii::t('lead', 'Status'),
			'user_id' => Yii::t('lead', 'User ID'),
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

	public static function getStatusesNames(): array {
		return [
			static::STATUS_ACTIVE => Yii::t('lead', 'Active'),
			static::STATUS_INACTIVE => Yii::t('lead', 'Inactive'),
			static::STATUS_DELETED => Yii::t('lead', 'Deleted'),
		];
	}
}
