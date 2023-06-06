<?php

namespace common\modules\lead\models;

use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_reminder".
 *
 * @property int $reminder_id
 * @property int $lead_id
 *
 * @property Lead $lead
 * @property Reminder $reminder
 */
class LeadReminder extends ActiveRecord implements ReminderInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_reminder}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['reminder_id', 'lead_id'], 'required'],
			[['reminder_id', 'lead_id'], 'integer'],
			[['reminder_id', 'lead_id'], 'unique', 'targetAttribute' => ['reminder_id', 'lead_id']],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['reminder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reminder::class, 'targetAttribute' => ['reminder_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'reminder_id' => Yii::t('lead', 'Reminder ID'),
			'reminder' => Yii::t('lead', 'Reminder'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
		];
	}

	/**
	 * Gets query for [[Lead]].
	 *
	 * @return ActiveQuery
	 */
	public function getLead() {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	/**
	 * Gets query for [[Reminder]].
	 *
	 * @return ActiveQuery
	 */
	public function getReminder() {
		return $this->hasOne(Reminder::class, ['id' => 'reminder_id']);
	}

	public function getUserId(): ?int {
		return $this->reminder->getUserId();
	}

	public function isDone(): bool {
		return $this->reminder->isDone();
	}

	public function isDelayed(): bool {
		return $this->reminder->isDelayed();
	}

	public function getDateAt(): string {
		return $this->reminder->getDateAt();
	}

	public function getDoneAt(): ?string {
		return $this->reminder->getDoneAt();
	}

	public function getPriority(): int {
		return $this->reminder->getPriority();
	}
}
