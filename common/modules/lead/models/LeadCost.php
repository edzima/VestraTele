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
 * @property Lead[] $leads
 */
class LeadCost extends ActiveRecord {

	public $leads_count;
	public $single_lead_cost_value;

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
			'value' => Yii::t('lead', 'Cost'),
			'singleLeadCostValue' => Yii::t('lead', 'Single Lead cost Value'),
			'leadsCount' => Yii::t('lead', 'Leads Count'),
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

	public function getSingleLeadCostValue(): ?float {
		if ($this->value && $this->getLeadsCount()) {
			return $this->value / count($this->leads);
		}
		return null;
	}

	public function getLeads(): ActiveQuery {
		if ($this->isNewRecord) {
			//@todo yii2 not allowed Expression in link attribute.
			$relation = $this->hasMany(Lead::class, ['campaign_id' => 'campaign_id']);
			$relation->onCondition([
				LeadCost::tableName() . '.date_at' => Lead::expressionDateAtAsDate(),
			]);
			return $relation;
		}

		return $this->hasMany(Lead::class, [
			'campaign_id' => 'campaign_id',
			'DATE(date_at)' => 'date_at',
		]);
	}

	public function getLeadsCount(): int {
		//@todo maybe internal cache for them
		return count($this->leads);
	}

	public static function batchUpsert(array $columns, array $rows): int {
		$command = static::getDb()
			->createCommand()
			->batchInsert(static::tableName(), $columns, $rows);
		$sql = $command->getRawSql();
		$sql .= ' ON DUPLICATE KEY UPDATE value=value';
		$command->setSql($sql);
		return $command->execute();
	}
}
