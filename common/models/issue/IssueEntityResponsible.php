<?php

namespace common\models\issue;


/**
 * This is the model class for table "issue_entity_responsible".
 *
 * @property int $id
 * @property string $name
 *
 * @property Issue[] $issues
 */
class IssueEntityResponsible extends \yii\db\ActiveRecord {

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
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::className(), ['entity_responsible_id' => 'id']);
	}

	public function __toString(): string {
		return $this->name;
	}
}
