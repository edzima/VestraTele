<?php

namespace common\models\entityResponsible;

use common\models\issue\Issue;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_entity_responsible".
 *
 * @property int $id
 * @property string $name
 * @property-read boolean $is_for_summon
 *
 * @property Issue[] $issues
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
			[['is_for_summon'], 'boolean'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
			'is_for_summon' => 'Dla wezwań',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['entity_responsible_id' => 'id']);
	}

	public static function find(): EntityResponsibleQuery {
		return new EntityResponsibleQuery(static::class);
	}

}
