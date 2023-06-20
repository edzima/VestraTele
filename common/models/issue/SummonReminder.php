<?php

namespace common\models\issue;

use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "summon_reminder".
 *
 * @property int $reminder_id
 * @property int $summon_id
 *
 * @property-read Reminder $reminder
 * @property-read Summon $summon
 */
class SummonReminder extends ActiveRecord implements ReminderInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_reminder}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['reminder_id', 'summon_id'], 'required'],
			[['reminder_id', 'summon_id'], 'integer'],
			[['reminder_id', 'summon_id'], 'unique', 'targetAttribute' => ['reminder_id', 'summon_id']],
			[['summon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Summon::class, 'targetAttribute' => ['summon_id' => 'id']],
			[['reminder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reminder::class, 'targetAttribute' => ['reminder_id' => 'id']],
		];
	}

	/**
	 * Gets query for [[Summon]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummon() {
		return $this->hasOne(Summon::class, ['id' => 'summon_id']);
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
		return $this->reminder->isDone() || $this->summon->isRealized();
	}

	public function isDelayed(): bool {
		return $this->reminder->isDelayed() || $this->summon->isDelayed();
	}

	public function getDateAt(): string {
		return $this->reminder->getDateAt();
	}

	public function getDoneAt(): ?string {
		return $this->reminder->getDoneAt() || $this->summon->realized_at;
	}

	public function getPriority(): int {
		return $this->reminder->getPriority();
	}
}
