<?php

namespace common\models;

use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "potential_client".
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property int $owner_id
 * @property string|null $details
 * @property int|null $city_id
 * @property string|null $birthday
 * @property int|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Simc $city
 * @property User $owner
 */
class PotentialClient extends ActiveRecord {

	public const STATUS_NEW = 0;
	public const STATUS_ADDRESS = 1;
	public const STATUS_CONTACT = 2;
	public const STATUS_NOT_QUALIFY = 5;
	public const STATUS_QUALIFIES_BUT_NOT_INTEREST = 10;
	public const STATUS_AGREEMENT = 20;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%potential_client}}';
	}

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
			[['firstname', 'lastname', '!owner_id', 'birthday', 'status'], 'required'],
			[['details'], 'string'],
			[['city_id', 'status'], 'integer'],
			[['birthday', 'created_at', 'updated_at'], 'safe'],
			[['firstname', 'lastname'], 'string', 'max' => 255],
			[['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Simc::class, 'targetAttribute' => ['city_id' => 'id']],
			[['!owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Who'),
			'details' => Yii::t('common', 'Details'),
			'city_id' => Yii::t('common', 'City'),
			'cityName' => Yii::t('common', 'City'),
			'birthday' => Yii::t('common', 'Birthday'),
			'status' => Yii::t('common', 'Status'),
			'statusName' => Yii::t('common', 'Status'),
			'created_at' => Yii::t('common', 'Created At'),
			'updated_at' => Yii::t('common', 'Updated At'),
			'ownerName' => Yii::t('common', 'Owner'),
			'firstname' => Yii::t('common', 'Firstname'),
			'lastname' => Yii::t('common', 'Lastname'),
		];
	}

	public function getName(): string {
		return $this->firstname . ' ' . $this->lastname;
	}

	public function isOwner(int $userId): bool {
		return $this->owner_id === $userId;
	}

	public function getCity() {
		return $this->hasOne(Simc::class, ['id' => 'city_id']);
	}

	public function getCityName(): ?string {
		$city = $this->city;
		if ($city) {
			return $city->getNameWithRegionAndDistrict();
		}
		return null;
	}

	public function getOwnerName(): string {
		return $this->owner->getFullName();
	}

	public function getOwner() {
		return $this->hasOne(User::class, ['id' => 'owner_id']);
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => Yii::t('common', 'New'),
			static::STATUS_ADDRESS => Yii::t('common', 'Address'),
			static::STATUS_QUALIFIES_BUT_NOT_INTEREST => Yii::t('common', 'Qualifies but not interest'),
			static::STATUS_NOT_QUALIFY => Yii::t('common', 'Not qualify'),
			static::STATUS_AGREEMENT => Yii::t('common', 'Agreement'),
			static::STATUS_CONTACT => Yii::t('common', 'Contact'),
		];
	}
}
