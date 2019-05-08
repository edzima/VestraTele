<?php

namespace common\models\issue;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_stage".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $posi
 *
 * @property Issue[] $issues
 * @property issueType[] $types
 */
class IssueStage extends \yii\db\ActiveRecord {

	public const ARCHIVES_ID = 6;

	public $typesIds = [];

	public function afterFind() {
		parent::afterFind();
		$this->typesIds = ArrayHelper::map($this->types, 'id', 'id');
	}

	public function beforeSave($insert) {
		if (!$insert) {
			$this->unlinkAll('types', true);
			foreach ($this->typesIds as $typeId) {
				$this->link('types', IssueType::get($typeId));
			}
		}

		return parent::beforeSave($insert);
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
			[['posi'], 'integer'],
			['posi', 'default', 'value' => 0],
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
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::className(), ['stage_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTypes() {
		return $this->hasMany(IssueType::className(), ['id' => 'type_id'])
			->viaTable('{{%issue_stage_type}}', ['stage_id' => 'id']);
	}

	public function getTypesName(): string {
		return implode(', ', $this->types);
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 * @return IssueStageQuery the active query used by this AR class.
	 */
	public static function find() {
		return new IssueStageQuery(get_called_class());
	}
}
