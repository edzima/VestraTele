<?php

namespace common\modules\reminder\models;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReminder;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "reminder".
 *
 * @property int $id
 * @property int $priority
 * @property int $created_at
 * @property int $updated_at
 * @property string $date_at
 * @property string|null $details
 *
 * @property LeadReminder[] $leadReminders
 * @property Lead[] $leads
 */
class Reminder extends ActiveRecord {

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
			[['date_at'], 'safe'],
			[['details'], 'string', 'max' => 255],
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
		];
	}

	/**
	 * Gets query for [[LeadReminders]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLeadReminders() {
		return $this->hasMany(LeadReminder::class, ['reminder_id' => 'id']);
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['id' => 'lead_id'])->viaTable('lead_reminder', ['reminder_id' => 'id']);
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
}
