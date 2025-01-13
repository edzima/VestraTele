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
 * @property int|null $order
 * @property boolean $show_in_grid
 * @property string $type
 *
 * @property-read LeadAnswer[] $answers
 * @property-read LeadReport[] $reports
 * @property-read LeadStatus|null $leadStatus
 * @property-read LeadType|null $leadType
 */
class LeadQuestion extends ActiveRecord {

	public const TYPE_TEXT = 'text';
	public const TYPE_TAG = 'tag';
	public const TYPE_BOOLEAN = 'boolean';
	public const TYPE_RADIO_GROUP = 'radio_group';

	public const RADIO_SELECTOR = '|';

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_question}}';
	}

	public static function sortByOrder(array &$models): void {
		usort($models, function (LeadQuestion $a, LeadQuestion $b) {
			return static::orderClosure($a, $b);
		});
	}

	public static function orderClosure(LeadQuestion $a, LeadQuestion $b): int {
		if (empty($a->order) && !empty($b->order)) {
			return 1;
		}
		if (empty($b->order) && !empty($a->order)) {
			return -1;
		}
		return $a->order <=> $b->order;
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
			'is_required' => Yii::t('lead', 'Is required'),
			'show_in_grid' => Yii::t('lead', 'Show in grid'),
			'order' => Yii::t('lead', 'Order'),
			'leadType' => Yii::t('lead', 'Lead Type'),
			'leadStatus' => Yii::t('lead', 'Lead Status'),
			'type' => Yii::t('lead', 'Type'),
		];
	}

	public function getQuestionWithAnswer($answer = null, string $template = '{question}: {answer}'): string {
		$answer = $this->generateAnswer($answer);
		if ($this->isTag()) {
			return $answer;
		}
		return strtr($template, [
			'{question}' => $this->name,
			'{answer}' => $answer,
		]);
	}

	public function generateAnswer(string $answer = null): string {
		if ($this->isTag() && $answer) {
			return $this->name;
		}
		if ($this->isBoolean()) {
			return Yii::$app->formatter->asBoolean($answer);
		}
		if ($answer === null) {
			$answer = Yii::$app->formatter->nullDisplay;
		}
		return $answer;
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

	public function getLeadStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	public function getLeadType(): ActiveQuery {
		return $this->hasOne(LeadType::class, ['id' => 'type_id']);
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
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

	public static function getTypesNames(): array {
		return [
			static::TYPE_TEXT => Yii::t('lead', 'Question - Text'),
			static::TYPE_BOOLEAN => Yii::t('lead', 'Question - Boolean'),
			static::TYPE_RADIO_GROUP => Yii::t('lead', 'Question - Radio'),
			static::TYPE_TAG => Yii::t('lead', 'Question - Tag'),
		];
	}

	public function isTag(): bool {
		return $this->type === self::TYPE_TAG;
	}

	public function isBoolean(): bool {
		return $this->type === self::TYPE_BOOLEAN;
	}

	public function isRadioGroup(): bool {
		return $this->type === self::TYPE_RADIO_GROUP;
	}

	public function isText(): bool {
		return $this->type === self::TYPE_TEXT;
	}

	public function getRadioValues(): array {
		return array_filter(
			explode(static::RADIO_SELECTOR, $this->placeholder)
		);
	}

	public function setRadioValues(array $values): void {
		$this->placeholder = implode(static::RADIO_SELECTOR, $values);
	}
}
