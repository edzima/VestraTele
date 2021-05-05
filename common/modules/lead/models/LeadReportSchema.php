<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_report_schema".
 *
 * @property int $id
 * @property string $name
 * @property string|null $placeholder
 * @property boolean $is_required
 * @property int|null $status_id
 * @property int|null $type_id
 * @property boolean $show_in_grid
 *
 * @property-read  LeadReport[] $reports
 * @property-read  LeadStatus|null $status
 * @property-read  LeadType|null $type
 */
class LeadReportSchema extends ActiveRecord {

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_report_schema}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'placeholder' => Yii::t('lead', 'Placeholder'),
			'type_id' => Yii::t('lead', 'Type'),
			'status_id' => Yii::t('lead', 'Status'),
			'is_required' => Yii::t('lead', 'Is required'),
			'show_in_grid' => Yii::t('lead', 'Show in grid'),
		];
	}

	public function getReports(): ActiveQuery {
		return $this->hasMany(LeadReport::class, ['schema_id' => 'id']);
	}

	public function getStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}

	/**
	 * @param int $status_id
	 * @param int $type_id
	 * @return static[]
	 */
	public static function findWithStatusAndType(int $status_id, int $type_id): array {
		return static::find()
			->andWhere(['or', ['status_id' => null], ['status_id' => $status_id]])
			->andWhere(['or', ['type_id' => null], ['type_id' => $type_id]])
			->all();
	}

}
