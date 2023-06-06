<?php

namespace common\modules\reminder\models;

use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReminder;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "reminder".
 *
 * @property int $id
 * @property int $priority
 * @property int $created_at
 * @property int $updated_at
 * @property string $date_at
 * @property string|null $done_at
 * @property int|null $user_id
 * @property string|null $details
 *
 * @property LeadReminder[] $leadReminders
 * @property Lead[] $leads
 * @property User|null $user
 */
class Reminder extends ActiveRecord implements ReminderInterface {

	public const PRIORITY_LOW = 0;
	public const PRIORITY_MEDIUM = 50;
	public const PRIORITY_HIGH = 100;

	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%reminder}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['priority', 'date_at'], 'required'],
			[['priority'], 'integer'],
			[['date_at', 'done_at'], 'safe'],
			[['details'], 'string', 'max' => 255],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
			[['user_id', 'done_at'], 'default', 'value' => null],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('common', 'ID'),
			'priority' => Yii::t('common', 'Priority'),
			'created_at' => Yii::t('common', 'Created At'),
			'updated_at' => Yii::t('common', 'Updated At'),
			'date_at' => Yii::t('common', 'Date At'),
			'details' => Yii::t('common', 'Details'),
			'user_id' => Yii::t('common', 'User'),
			'user' => Yii::t('common', 'User'),
		];
	}

	/**
	 * Gets query for [[LeadReminders]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeadReminders() {
		return $this->hasMany(LeadReminder::class, ['reminder_id' => 'id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['id' => 'lead_id'])->viaTable(LeadReminder::tableName(), ['reminder_id' => 'id']);
	}

	public function getPriorityName(): string {
		return static::getPriorityNames()[$this->priority];
	}

	public static function getPriorityNames(): array {
		return [
			static::PRIORITY_LOW => Yii::t('common', 'Low'),
			static::PRIORITY_MEDIUM => Yii::t('common', 'Medium'),
			static::PRIORITY_HIGH => Yii::t('common', 'High'),
		];
	}

	public static function find(): ReminderQuery {
		return new ReminderQuery(static::class);
	}

	public function isDone(): bool {
		return !empty($this->done_at);
	}

	public function isDelayed(): bool {
		return !$this->isDone()
			&& (strtotime($this->date_at) < time());
	}

	public function markAsDone(): void {
		$this->done_at = date(DATE_ATOM);
	}

	public function unmarkAsDone(): void {
		$this->done_at = null;
	}

	public function getUserId(): ?int {
		return $this->user_id;
	}

	public function getDateAt(): string {
		return $this->date_at;
	}

	public function getDoneAt(): ?string {
		return $this->done_at;
	}

	public function getPriority(): int {
		return $this->priority;
	}
}
