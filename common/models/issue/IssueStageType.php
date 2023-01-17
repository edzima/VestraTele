<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_stage_type".
 *
 * @property int $stage_id
 * @property int $type_id
 * @property int|null $days_reminder
 * @property int|null $days_reminder_second
 * @property int|null $days_reminder_third
 * @property int|null $days_reminder_fourth
 * @property int|null $days_reminder_fifth
 * @property string|null $calendar_background
 * @property int|null $min_calculation_count
 *
 * @property-read IssueType[] $types
 * @property-read IssueStage[] $stages
 */
class IssueStageType extends ActiveRecord {

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('issue', 'Stage'),
			'type_id' => Yii::t('issue', 'Type'),
			'stageName' => Yii::t('issue', 'Stage'),
			'typeName' => Yii::t('issue', 'Type'),
			'days_reminder' => Yii::t('issue', '1. Reminder (days)'),
			'days_reminder_second' => Yii::t('issue', '2. Reminder (days)'),
			'days_reminder_third' => Yii::t('issue', '3. Reminder (days)'),
			'days_reminder_fourth' => Yii::t('issue', '4. Reminder (days)'),
			'days_reminder_fifth' => Yii::t('issue', '5. Reminder (days)'),
			'calendar_background' => Yii::t('common', 'Calendar Background'),
		];
	}

	public function getDaysReminders(): array {
		$days = [];
		$days[] = $this->days_reminder;
		$days[] = $this->days_reminder_second;
		$days[] = $this->days_reminder_third;
		$days[] = $this->days_reminder_fourth;
		$days[] = $this->days_reminder_fifth;
		return array_filter($days, function ($value): bool {
			return $value > 0;
		});
	}

	public function getTypeName(): string {
		return IssueType::getTypesNames()[$this->type_id];
	}

	public function getStageName(): string {
		return IssueStage::getStagesNames(true)[$this->stage_id];
	}

	public function getStages(): ActiveQuery {
		return $this->hasMany(IssueStage::class, ['stage_id' => 'id']);
	}

	public function getTypes(): ActiveQuery {
		return $this->hasMany(IssueType::class, ['type_id' => 'id']);
	}
}
