<?php

namespace common\modules\lead\models;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\query\LeadQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "lead_market".
 *
 * @property int $id
 * @property int $lead_id
 * @property int $status
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $options
 *
 * @property Lead $lead
 * @property LeadMarketUser[] $leadMarketUsers
 */
class LeadMarket extends ActiveRecord {

	public const STATUS_NEW = 1;

	private ?LeadMarketOptions $marketOptions = null;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_market}}';
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => Yii::t('lead', 'New'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('NOW()'),
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'status'], 'required'],
			[['lead_id', 'status'], 'integer'],
			[['options', 'details'], 'string'],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
			'status' => Yii::t('lead', 'Status'),
			'details' => Yii::t('lead', 'Details'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
			'options' => Yii::t('lead', 'Options'),
		];
	}

	public function getLead(): LeadQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getMarketOptions(): LeadMarketOptions {
		if ($this->marketOptions === null) {
			$this->marketOptions = new LeadMarketOptions(Json::decode($this->options));
		}
		return $this->marketOptions;
	}

	/**
	 * Gets query for [[LeadMarketUsers]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeadMarketUsers() {
		return $this->hasMany(LeadMarketUser::class, ['lead_id' => 'id']);
	}
}
