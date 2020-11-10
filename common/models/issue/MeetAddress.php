<?php

namespace common\models\issue;

use common\models\Address;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "meet_address".
 *
 * @property int $meet_id
 * @property int $address_id
 * @property int $type
 *
 * @property-read IssueMeet $meet
 * @property-read Address $address
 */
class MeetAddress extends ActiveRecord {

	public const TYPE_CUSTOMER = 1;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'meet_address';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['meet_id', 'address_id', 'type'], 'required'],
			[['meet_id', 'address_id', 'type'], 'integer'],
			[['meet_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueMeet::class, 'targetAttribute' => ['meet_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'meet_id' => Yii::t('address', 'Meet ID'),
			'address_id' => Yii::t('address', 'Address ID'),
			'type' => Yii::t('address', 'Type'),
		];
	}

	/**
	 * Gets query for [[Meet]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getMeet() {
		return $this->hasOne(IssueMeet::class, ['id' => 'meet_id']);
	}

	public function getAddress() {
		return $this->hasOne(Address::class, ['id' => 'address_id']);
	}
}
