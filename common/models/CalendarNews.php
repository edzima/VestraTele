<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "calendar_news".
 *
 * @property int $id
 * @property string $news
 * @property int $agent_id
 * @property string $start
 * @property string $end
 */
class CalendarNews extends ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'calendar_news';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['news', 'agent_id'], 'required'],
			[['news'], 'string'],
			[['agent_id'], 'integer'],
			[['start', 'end'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'news' => 'News',
			'agent_id' => 'Agent ID',
			'start' => 'Start',
			'end' => 'End',
		];
	}
}
