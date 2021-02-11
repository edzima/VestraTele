<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_report_schema_status_type".
 *
 * @property int $schema_id
 * @property int $status_id
 * @property int $type_id
 *
 * @property LeadReportSchema $schema
 * @property LeadStatus $status
 * @property LeadType $type
 */
class LeadReportSchemaStatusType extends ActiveRecord {

	public static function findSchemasByStatusAndType(int $status_id, int $type_id): array {
		$models = static::find()
			->joinWith('schema')
			->andWhere([
				'status_id' => $status_id,
				'type_id' => $type_id,
			])
			->all();
		$schemas = [];
		foreach ($models as $model) {
			/** @var static $model */
			$schemas[$model->schema_id] = $model->schema;
		}
		return $schemas;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_report_schema_status_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['schema_id', 'status_id', 'type_id'], 'required'],
			[['schema_id', 'status_id', 'type_id'], 'integer'],
			[['schema_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadReportSchema::class, 'targetAttribute' => ['schema_id' => 'id']],
			[['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['status_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadType::class, 'targetAttribute' => ['type_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'schema_id' => Yii::t('lead', 'Schema ID'),
			'status_id' => Yii::t('lead', 'Status ID'),
			'type_id' => Yii::t('lead', 'Type ID'),
		];
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
	 * Gets query for [[Type]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}
}
