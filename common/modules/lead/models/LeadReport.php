<?php

namespace common\modules\lead\models;

use common\modules\lead\models\query\LeadReportQuery;
use common\modules\lead\Module;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_report".
 *
 * @property int $id
 * @property int $lead_id
 * @property int $owner_id
 * @property int $status_id
 * @property int $old_status_id
 * @property int $schema_id
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Lead $lead
 * @property LeadStatus $oldStatus
 * @property ActiveRecord $owner
 * @property LeadReportSchema $schema
 * @property LeadStatus $status
 */
class LeadReport extends ActiveRecord {

	public function getFormattedDetails(): string {
		if ($this->schema->placeholder) {
			return $this->details;
		}
		return Yii::$app->formatter->asBoolean($this->details);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_report}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'owner_id', 'status_id', 'old_status_id', 'schema_id'], 'required'],
			[['lead_id', 'owner_id', 'status_id', 'old_status_id', 'schema_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['details'], 'string', 'max' => 255],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['old_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['old_status_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
			[['schema_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadReportSchema::class, 'targetAttribute' => ['schema_id' => 'id']],
			[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['status_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
			'owner_id' => Yii::t('lead', 'Owner ID'),
			'status_id' => Yii::t('lead', 'Status ID'),
			'old_status_id' => Yii::t('lead', 'Old Status ID'),
			'schema_id' => Yii::t('lead', 'Schema ID'),
			'details' => Yii::t('lead', 'Details'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
		];
	}

	/**
	 * Gets query for [[Lead]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLead() {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	/**
	 * Gets query for [[OldStatus]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOldStatus() {
		return $this->hasOne(LeadStatus::class, ['id' => 'old_status_id']);
	}

	/**
	 * Gets query for [[Owner]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOwner() {
		return $this->hasOne(Module::userClass(), ['id' => 'owner_id']);
	}

	/**
	 * Gets query for [[Schema]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSchema() {
		return $this->hasOne(LeadReportSchema::class, ['id' => 'schema_id']);
	}

	/**
	 * Gets query for [[Status]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatus() {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function find(): LeadReportQuery {
		return new LeadReportQuery(static::class);
	}
}
