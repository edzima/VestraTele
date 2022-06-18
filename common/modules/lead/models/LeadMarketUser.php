<?php

namespace common\modules\lead\models;

use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\Module;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_market_user".
 *
 * @property int $id
 * @property int $market_id
 * @property int $lead_id
 * @property int $user_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property LeadMarket $market
 */
class LeadMarketUser extends ActiveRecord {

	public const STATUS_TO_CONFIRM = 1;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_market_user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['market_id', 'lead_id', 'status', 'user_id'], 'required'],
			[['market_id', 'lead_id', 'status', 'user_id'], 'integer'],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadMarket::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'market_id' => Yii::t('lead', 'Market ID'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
			'user_id' => Yii::t('lead', 'User ID'),
			'status' => Yii::t('lead', 'Status'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
		];
	}

	/**
	 * Gets query for [[Lead]].
	 *
	 * @return ActiveQuery
	 */
	public function getMarket(): LeadQuery {
		return $this->hasOne(LeadMarket::class, ['id' => 'lead_id']);
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id']);
	}
}
