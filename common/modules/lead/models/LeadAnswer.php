<?php

namespace common\modules\lead\models;

use common\modules\lead\models\query\LeadAnswerQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_answer".
 *
 * @property int $report_id
 * @property int $question_id
 * @property string|null $answer
 *
 * @property LeadQuestion $question
 * @property LeadReport $report
 * @property-read string $answerQuestion
 */
class LeadAnswer extends ActiveRecord {

	public static function orderByQuestions(array &$models): void {
		usort($models, function (LeadAnswer $a, LeadAnswer $b) {
			return LeadQuestion::orderClosure($a->question, $b->question);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_answer}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['report_id', 'question_id'], 'required'],
			[['report_id', 'question_id'], 'integer'],
			[['answer'], 'string', 'max' => 255],
			[['report_id', 'question_id'], 'unique', 'targetAttribute' => ['report_id', 'question_id']],
			[['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadQuestion::class, 'targetAttribute' => ['question_id' => 'id']],
			[
				'answer', 'required',
				'when' => function (): bool {
					return $this->question->hasPlaceholder();
				},
				'enableClientValidation' => false,
			],
			[['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadReport::class, 'targetAttribute' => ['report_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'report_id' => Yii::t('lead', 'Report ID'),
			'question_id' => Yii::t('lead', 'Question ID'),
			'answer' => Yii::t('lead', 'Answer'),
		];
	}

	public function getAnswerQuestion(): string {
		return $this->question->generateAnswer($this->answer);
	}

	/**
	 * Gets query for [[Question]].
	 *
	 * @return ActiveQuery
	 */
	public function getQuestion(): ActiveQuery {
		return $this->hasOne(LeadQuestion::class, ['id' => 'question_id']);
	}

	/**
	 * Gets query for [[Report]].
	 *
	 * @return ActiveQuery
	 */
	public function getReport(): ActiveQuery {
		return $this->hasOne(LeadReport::class, ['id' => 'report_id']);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function find(): LeadAnswerQuery {
		return new LeadAnswerQuery(static::class);
	}
}
