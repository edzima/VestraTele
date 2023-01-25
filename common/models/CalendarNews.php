<?php

namespace common\models;

use common\models\user\query\UserQuery;
use common\models\user\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calendar_news".
 *
 * @property int $id
 * @property string $text
 * @property int $user_id
 * @property string $start_at
 * @property string $end_at
 * @property string $type
 *
 * @property-read User $user
 */
class CalendarNews extends ActiveRecord {

	public const TYPE_ISSUE_STAGE_DEADLINE = 'issue.stage.deadline';
	public const TYPE_SUMMON = 'summon';
	public const TYPE_LEAD_REMINDER = 'lead.reminder';

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%calendar_news}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['text', 'user_id'], 'required'],
			[['text'], 'string'],
			[['user_id'], 'integer'],
			[['start_at', 'end_at'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'text' => 'Text',
			'user_id' => 'User',
			'start_at' => 'Start',
			'end_at' => 'End',
		];
	}

	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
