<?php

namespace common\models\issue;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_stage_type".
 *
 * @property int $stage_id
 * @property int $type_id
 * @property int|null $days_reminder
 * @property string|null $calendar_background
 * @property int|null $min_calculation_count
 *
 * @property-read IssueType[] $types
 * @property-read IssueStage[] $stages
 */
class IssueStageType extends ActiveRecord {

	public function getTypeName(): string {
		return IssueType::getTypesNames()[$this->type_id];
	}

	public function getStages(): ActiveQuery {
		return $this->hasMany(IssueStage::class, ['stage_id' => 'id']);
	}

	public function getTypes(): ActiveQuery {
		return $this->hasMany(IssueType::class, ['type_id' => 'id']);
	}
}
