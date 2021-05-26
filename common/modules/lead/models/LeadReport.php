<?php

namespace common\modules\lead\models;

use common\behaviors\DatesInfoBehavior;
use common\modules\lead\models\query\LeadReportQuery;
use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_report".
 *
 * @property int $id
 * @property int $lead_id
 * @property int $owner_id
 * @property int $status_id
 * @property int $old_status_id
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read Lead $lead
 * @property-read LeadStatus $oldStatus
 * @property-read ActiveRecord $owner
 * @property-read LeadStatus $status
 * @property-read LeadAnswer[] $answers
 *
 * @property-read string $answersQuestions
 * @property-read string $formattedDates
 */
class LeadReport extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_report}}';
	}

	public function behaviors() {
		return [
			'dateInfo' => DatesInfoBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'owner_id', 'status_id', 'old_status_id'], 'required'],
			[['lead_id', 'owner_id', 'status_id', 'old_status_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['details'], 'string', 'max' => 255],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['old_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatus::class, 'targetAttribute' => ['old_status_id' => 'id']],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
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
			'details' => Yii::t('lead', 'Details'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
		];
	}

	public function isChangeStatus(): bool {
		return $this->old_status_id !== $this->status_id;
	}

	public function getAnswersQuestions(): string {
		$answers = $this->answers;
		$questionAnswers = [];
		foreach ($answers as $answer) {
			$questionAnswers[] = $answer->getAnswerQuestion();
		}
		return implode(', ', $questionAnswers);
	}

	public function getAnswer(int $question_id): ?LeadAnswer {
		return $this->answers[$question_id] ?? null;
	}

	/**
	 * Gets query for [[LeadAnswers]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getAnswers() {
		return $this->hasMany(LeadAnswer::class, ['report_id' => 'id'])->indexBy('question_id');
	}

	/**
	 * Gets query for [[Questions]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getQuestions() {
		return $this->hasMany(LeadQuestion::class, ['id' => 'question_id'])->viaTable(LeadAnswer::tableName(), ['report_id' => 'id']);
	}

	public function getOwnerId(): int {
		return $this->owner_id;
	}

	public function getDetails(): ?string {
		return $this->details;
	}

	public function getLead(): ActiveQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getOldStatus() {
		return $this->hasOne(LeadStatus::class, ['id' => 'old_status_id']);
	}

	public function getOwner() {
		return $this->hasOne(Module::userClass(), ['id' => 'owner_id']);
	}

	public function getStatus(): ActiveQuery {
		return $this->hasOne(LeadStatus::class, ['id' => 'status_id']);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function find(): LeadReportQuery {
		return new LeadReportQuery(static::class);
	}

}
