<?php

namespace common\models\hint;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hint_city_source".
 *
 * @property int $source_id
 * @property int $hint_id
 * @property string $phone
 * @property string $rating
 * @property string $status
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 *
 * @property HintCity $hint
 * @property HintSource $source
 *
 */
class HintCitySource extends ActiveRecord {

	public const STATUS_RENEW = 'renew';
	public const STATUS_UNINTERESTED = 'uninterested';
	public const STATUS_NOT_ANSWER = 'not-answer';
	public const STATUS_SHIPPED_MATERIALS = 'shipped-materials';

	public const RATING_POSITIVE = 'positive';
	public const RATING_NEUTRAL = 'neutral';
	public const RATING_NEGATIVE = 'negative';

	public static function getStatusesNames(): array {
		return [
			static::STATUS_RENEW => Yii::t('hint', 'Renew'),
			static::STATUS_UNINTERESTED => Yii::t('hint', 'Uninterested'),
			static::STATUS_SHIPPED_MATERIALS => Yii::t('hint', 'Shipped materials'),
			static::STATUS_NOT_ANSWER => Yii::t('hint', 'Not answer'),
		];
	}

	public static function getRatingsNames(): array {
		return [
			static::RATING_POSITIVE => Yii::t('hint', 'Positive'),
			static::RATING_NEUTRAL => Yii::t('hint', 'Neutral'),
			static::RATING_NEGATIVE => Yii::t('hint', 'Negative'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%hint_city_source}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['source_id', 'hint_id', 'phone', 'rating', 'status'], 'required'],
			[['source_id', 'hint_id'], 'integer'],
			[['details', 'status'], 'string'],
			[['phone', 'rating'], 'string', 'max' => 50],
			[['source_id', 'hint_id'], 'unique', 'targetAttribute' => ['source_id', 'hint_id']],
			['rating', 'in', 'range' => array_keys(static::getRatingsNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[['hint_id'], 'exist', 'skipOnError' => true, 'targetClass' => HintCity::class, 'targetAttribute' => ['hint_id' => 'id']],
			[['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => HintSource::class, 'targetAttribute' => ['source_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'source_id' => Yii::t('hint', 'Source'),
			'hint_id' => Yii::t('hint', 'Hint'),
			'phone' => Yii::t('hint', 'Phone'),
			'rating' => Yii::t('hint', 'Rating'),
			'ratingName' => Yii::t('hint', 'Rating'),
			'status' => Yii::t('hint', 'Status'),
			'statusName' => Yii::t('hint', 'Status'),
			'details' => Yii::t('hint', 'Details'),
			'created_at' => Yii::t('hint', 'Created At'),
			'updated_at' => Yii::t('hint', 'Updated At'),
		];
	}

	public function getRatingName(): string {
		return static::getRatingsNames()[$this->rating];
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	/**
	 * Gets query for [[Hint]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getHint() {
		return $this->hasOne(HintCity::class, ['id' => 'hint_id']);
	}

	/**
	 * Gets query for [[Source]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSource() {
		return $this->hasOne(HintSource::class, ['id' => 'source_id']);
	}
}
