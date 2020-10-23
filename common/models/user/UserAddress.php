<?php

namespace common\models\user;

use common\models\Address;
use common\models\user\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_address".
 *
 * @property int $user_id
 * @property int $address_id
 * @property int $type
 *
 * @property-read  User $user
 * @property-read Address $address
 */
class UserAddress extends ActiveRecord {

	public const TYPE_HOME = 1;
	public const TYPE_POSTAL = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%user_address}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'address_id', 'type'], 'required'],
			[['user_id', 'address_id', 'type'], 'integer'],
			[['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::class, 'targetAttribute' => ['address_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('address', 'User'),
			'address_id' => Yii::t('address', 'Address'),
			'type' => Yii::t('address', 'Type'),
		];
	}

	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getAddress(): ActiveQuery {
		return $this->hasOne(Address::class, ['id' => 'address_id']);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_HOME => Yii::t('address', 'Home'),
			static::TYPE_POSTAL => Yii::t('address', 'Postal'),
		];
	}
}
