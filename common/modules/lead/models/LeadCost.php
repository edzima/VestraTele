<?php

namespace common\modules\lead\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "lead_cost".
 *
 * @property int $id
 * @property int $campaign_id
 * @property float|null $value
 * @property string $date_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property LeadCampaign $campaign
 */
class LeadCost extends ActiveRecord {

	public function behaviors(): array {
		return array_merge(parent::behaviors(), [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),

			],
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_cost}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['campaign_id', 'date_at'], 'required'],
			[['campaign_id'], 'integer'],
			[['value'], 'number'],
			[['date_at', 'created_at', 'updated_at'], 'safe'],
			[['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadCampaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'campaign' => Yii::t('lead', 'Campaign'),
			'campaign_id' => Yii::t('lead', 'Campaign'),
			'value' => Yii::t('lead', 'Value'),
			'date_at' => Yii::t('lead', 'Date At'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
		];
	}

	/**
	 * Gets query for [[Campaign]].
	 *
	 * @return ActiveQuery
	 */
	public function getCampaign() {
		return $this->hasOne(LeadCampaign::class, ['id' => 'campaign_id']);
	}

	public function getName(): string {
		return $this->campaign->name . ': ' . Yii::$app->formatter->asCurrency($this->value);
	}
}
