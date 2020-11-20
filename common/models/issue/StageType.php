<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class StageType
 *
 * @property int $type_id
 * @property int $stage_id
 * @property int|null $min_calculation_count
 *
 * @property-read IssueStage $stage
 * @property-read IssueType $type
 */
class StageType extends ActiveRecord {

	public static function primaryKey(): array {
		return [
			'stage_id',
			'type_id',
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_stage_type}}';
	}

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('backend', 'Stage'),
			'type_id' => Yii::t('backend', 'Type'),
			'min_calculation_count' => Yii::t('backend', 'Min calculation count'),
		];
	}

	public function getStage(): ActiveQuery {
		return $this->hasOne(IssueStage::class, ['id' => 'stage_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(IssueType::class, ['id' => 'type_id']);
	}
}
