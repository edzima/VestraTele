<?php

namespace common\models;

use common\models\issue\IssueMeet;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "campaign".
 *
 * @property int $id
 * @property string $name
 *
 * @property IssueMeet[] $issueMeets
 */
class Campaign extends ActiveRecord {

	public function __toString() {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'campaign';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 50],
			[['name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}

	public function getIssueMeets(): ActiveQuery {
		return $this->hasMany(IssueMeet::class, ['campaign_id' => 'id']);
	}
}
