<?php

namespace common\modules\lead\models;

use common\modules\reminder\models\Reminder;
use Yii;

/**
 * This is the model class for table "lead_reminder".
 *
 * @property int $reminder_id
 * @property int $lead_id
 *
 * @property Lead $lead
 * @property Reminder $reminder
 */
class LeadReminder extends \yii\db\ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'lead_reminder';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
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
	public function attributeLabels() {
		return [
			'reminder_id' => Yii::t('lead', 'Reminder ID'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
		];
	}

	/**
	 * Gets query for [[Lead]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLead() {
		return $this->hasOne(Lead::className(), ['id' => 'lead_id']);
	}

	/**
	 * Gets query for [[Reminder]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getReminder() {
		return $this->hasOne(Reminder::className(), ['id' => 'reminder_id']);
	}
}
