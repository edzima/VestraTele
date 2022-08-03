<?php

namespace common\modules\lead\models;

use common\models\Address;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "lead_address".
 *
 * @property int $lead_id
 * @property int $address_id
 * @property int $type
 *
 * @property-read Lead $lead
 * @property-read Address $address
 */
class LeadAddress extends ActiveRecord {

	public const TYPE_CUSTOMER = 1;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_address}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'address_id', 'type'], 'required'],
			[['lead_id', 'address_id', 'type'], 'integer'],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::class, 'targetAttribute' => ['address_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'lead_id' => Yii::t('address', 'Lead ID'),
			'address_id' => Yii::t('address', 'Address ID'),
			'type' => Yii::t('address', 'Type'),
		];
	}

	public function getLead(): ActiveQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getAddress(): ActiveQuery {
		return $this->hasOne(Address::class, ['id' => 'address_id']);
	}
}
