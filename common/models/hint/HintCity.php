<?php

namespace common\models\hint;

use common\models\user\User;
use edzima\teryt\models\Simc;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hint_city".
 *
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property string $type
 * @property string $status
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Simc $city
 * @property User $user
 * @property HintCitySource[] $hintCitySources
 * @property HintSource[] $sources
 *
 */
class HintCity extends ActiveRecord {

	public const STATUS_NEW = 'new';
	public const STATUS_IN_PROGRESS = 'in_progress';
	public const STATUS_ABANDONED = 'abandoned';
	public const STATUS_DONE = 'done';

	public const TYPE_CARE_BENEFITS = 'care_benefits';
	public const TYPE_COMMISSION_REFUNDS = 'commission_refunds';

	public function getCityNameWithType(): string {
		return $this->city->name . ' - ' . $this->getTypeName();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%hint_city}}';
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_CARE_BENEFITS => Yii::t('hint', 'Care benefits'),
			static::TYPE_COMMISSION_REFUNDS => Yii::t('hint', 'Commission refunds'),
		];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => Yii::t('hint', 'New'),
			static::STATUS_IN_PROGRESS => Yii::t('hint', 'In progress'),
			static::STATUS_ABANDONED => Yii::t('hint', 'Abandoned'),
			static::STATUS_DONE => Yii::t('hint', 'Done'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('hint', 'ID'),
			'user_id' => Yii::t('hint', 'User'),
			'city_id' => Yii::t('hint', 'City'),
			'type' => Yii::t('hint', 'Type'),
			'typeName' => Yii::t('hint', 'Type'),
			'status' => Yii::t('hint', 'Status'),
			'statusName' => Yii::t('hint', 'Status'),
			'details' => Yii::t('hint', 'Details'),
			'created_at' => Yii::t('hint', 'Created At'),
			'updated_at' => Yii::t('hint', 'Updated At'),
		];
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * Gets query for [[City]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCity() {
		return $this->hasOne(Simc::class, ['id' => 'city_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/**
	 * Gets query for [[HintCitySources]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getHintCitySources() {
		return $this->hasMany(HintCitySource::class, ['hint_id' => 'id']);
	}

	/**
	 * Gets query for [[Sources]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSources() {
		return $this->hasMany(HintSource::class, ['id' => 'source_id'])->viaTable('hint_city_source', ['hint_id' => 'id']);
	}
}
