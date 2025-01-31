<?php

namespace common\modules\court\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lawsuit_sessions".
 *
 * @property int $id
 * @property string|null $details
 * @property int $lawsuit_id
 * @property string $date_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $room
 * @property int|null $is_cancelled
 * @property int|null $presence_of_the_claimant
 * @property string|null $location
 * @property int $creator_id
 * @property string|null $url
 *
 * @property Lawsuit $lawsuit
 */
class LawsuitSession extends ActiveRecord {

	public const LOCATION_STATIONARY = 'S';
	public const LOCATION_ONLINE = 'O';

	public const PRESENCE_OF_THE_CLAIMANT_REQUIRED = 1;
	public const PRESENCE_OF_THE_CLAIMANT_NOT_REQUIRED = 0;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lawsuit_sessions}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['details'], 'string'],
			[['lawsuit_id', 'date_at', 'created_at', 'updated_at'], 'required'],
			[['lawsuit_id', 'is_cancelled', 'presence_of_the_claimant'], 'integer'],
			[['date_at', 'created_at', 'updated_at'], 'safe'],
			[['room'], 'string', 'max' => 255],
			[['lawsuit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lawsuit::class, 'targetAttribute' => ['lawsuit_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('court', 'ID'),
			'details' => Yii::t('court', 'Details'),
			'lawsuit_id' => Yii::t('court', 'Lawsuit ID'),
			'date_at' => Yii::t('court', 'Due At'),
			'created_at' => Yii::t('court', 'Created At'),
			'updated_at' => Yii::t('court', 'Updated At'),
			'room' => Yii::t('court', 'Room'),
			'is_cancelled' => Yii::t('court', 'Is Cancelled'),
			'presence_of_the_claimant' => Yii::t('court', 'Presence of the Claimant'),
			'presenceOfTheClaimantName' => Yii::t('court', 'Presence of the Claimant'),
			'location' => Yii::t('court', 'Location'),
			'locationName' => Yii::t('court', 'Location'),
			'url' => Yii::t('court', 'URL'),
		];
	}

	public function isAfterDueAt(): ?bool {
		if (empty($this->date_at)) {
			return null;
		}
		return strtotime($this->date_at) < strtotime('now');
	}

	public function getLocationName(): ?string {
		return static::getLocationNames()[$this->location] ?? null;
	}

	public function getPresenceOfTheClaimantName(): ?string {
		return static::getPresenceOfTheClaimantNames()[$this->presence_of_the_claimant] ?? null;
	}

	public static function getLocationNames(): array {
		return [
			static::LOCATION_STATIONARY => Yii::t('court', 'Stationary'),
			static::LOCATION_ONLINE => Yii::t('court', 'Online'),
		];
	}

	public static function getPresenceOfTheClaimantNames(): array {
		return [
			static::PRESENCE_OF_THE_CLAIMANT_REQUIRED => Yii::t('court', 'Required'),
			static::PRESENCE_OF_THE_CLAIMANT_NOT_REQUIRED => Yii::t('court', 'Not required'),
		];
	}

	/**
	 * Gets query for [[Lawsuit]].
	 *
	 * @return ActiveQuery
	 */
	public function getLawsuit() {
		return $this->hasOne(Lawsuit::class, ['id' => 'lawsuit_id']);
	}
}
