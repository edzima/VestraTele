<?php

namespace common\models\entityResponsible;

use common\models\address\City;
use common\models\issue\Issue;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_entity_responsible".
 *
 * @property int $id
 * @property string $name
 *
 * @property Issue[] $issues
 * @property City[] $cities
 */
class EntityResponsible extends ActiveRecord {

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue_entity_responsible';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['entity_responsible_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCities() {
		return $this->hasMany(City::class, ['id' => 'city_id'])->viaTable('issue_entity_responsible_details', ['entity_id' => 'id']);
	}

}
