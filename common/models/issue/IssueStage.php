<?php

namespace common\models\issue;

use common\models\issue\query\IssueStageQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_stage".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $posi
 * @property int $days_reminder
 *
 * @property Issue[] $issues
 * @property issueType[] $types
 */
class IssueStage extends ActiveRecord {

	public const ARCHIVES_ID = 6;
	public const POSITIVE_DECISION_ID = 18;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue_stage';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name', 'short_name', 'typesIds'], 'required'],
			[['posi', 'days_reminder'], 'integer'],
			['posi', 'default', 'value' => 0],
			['days_reminder', 'integer', 'min' => 1],
			[['name', 'short_name'], 'string', 'max' => 255],
			[['name', 'short_name'], 'unique'],
			[['short_name'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
			'short_name' => 'Skrót',
			'posi' => 'Pozycja',
			'typesIds' => 'Typy',
			'days_reminder' => 'Przypomnij(dni)',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['stage_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTypes() {
		return $this->hasMany(IssueType::class, ['id' => 'type_id'])
			->viaTable('{{%issue_stage_type}}', ['stage_id' => 'id']);
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public function getTypesName(): string {
		return implode(', ', $this->types);
	}

	/**
	 * @inheritdoc
	 * @return IssueStageQuery the active query used by this AR class.
	 */
	public static function find() {
		return new IssueStageQuery(get_called_class());
	}

}
