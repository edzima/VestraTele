<?php

namespace common\modules\lead\models;

use common\modules\lead\models\query\LeadQuestionQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_question".
 *
 * @property int $id
 * @property string $name
 * @property string|null $placeholder
 * @property boolean $is_required
 * @property boolean $is_active
 * @property int|null $status_id
 * @property int|null $type_id
 * @property boolean $show_in_grid
 * @property boolean $is_boolean
 *
 * @property-read LeadAnswer[] $answers
 * @property-read LeadReport[] $reports
 * @property-read LeadStatus|null $status
 * @property-read LeadType|null $type
 */
class LeadQuestion extends ActiveRecord {

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_question}}';
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
			'is_active' => Yii::t('lead', 'Is Active'),
			'is_boolean' => Yii::t('lead', 'Is Boolean'),
			'is_required' => Yii::t('lead', 'Is required'),
			'show_in_grid' => Yii::t('lead', 'Show in grid'),
		];
	}

	public function isClosed(): bool {
		if ($this->hasPlaceholder()) {
			return false;
		}
		if ($this->is_boolean) {
			return false;
		}
		return true;
	}

	public function generateAnswer(string $answer = null): string {
		if ($this->hasPlaceholder() || $this->is_boolean) {
			if ($this->is_boolean) {
				$answer = Yii::$app->formatter->asBoolean($answer);
			}
			if ($answer === null) {
				$answer = Yii::$app->formatter->nullDisplay;
			}
			return $this->name . ': ' . $answer;
		}
		return $this->name;
	}

	public function hasPlaceholder(): bool {
		return !empty($this->placeholder);
	}

	public function getAnswers(): ActiveQuery {
		return $this->hasMany(LeadAnswer::class, ['question_id' => 'id']);
	}

	public function getReports(): ActiveQuery {
		return $this->hasMany(LeadReport::class, ['id' => 'report_id'])->viaTable(LeadAnswer::tableName(), ['question_id' => 'id']);
	}

	public function getStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}

	public static function find(): LeadQuestionQuery {
		return new LeadQuestionQuery(static::class);
	}

	/**
	 * @param int $status_id
	 * @param int $type_id
	 * @return static[]
	 */
	public static function findWithStatusAndType(int $status_id, int $type_id): array {
		return static::find()
			->forStatus($status_id)
			->forType($type_id)
			->all();
	}

}
