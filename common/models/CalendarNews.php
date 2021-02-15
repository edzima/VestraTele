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
 *
 * @property-read User $user
 */
class CalendarNews extends ActiveRecord {

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

	public static function getFilters(): array {
		return [
			[
				'value' => true,
				'isActive' => true,
				'label' => 'notatka',
				'color' => '#009688',
				'eventColors' => [
					'background' => '#009688',
				],
			]
		];
	}

	public function getUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
