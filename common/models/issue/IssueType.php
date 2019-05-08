<?php

namespace common\models\issue;

use yii\base\InvalidParamException;

/**
 * This is the model class for table "issue_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $provision_type
 *
 * @property Issue[] $issues
 * @property IssueStage[] $stages
 * @property Provision $provision
 */
class IssueType extends \yii\db\ActiveRecord {

	private $provision;

	private static $TYPES = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue_type';
	}

	public static function get(int $typeId) {
		if (!isset(static::$TYPES[$typeId])) {
			$model = static::findOne($typeId);
			if ($model === null) {
				throw new InvalidParamException('Invalid type id: ' . $typeId);
			}
			static::$TYPES[$typeId] = $model;
		}
		return static::$TYPES[$typeId];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name', 'short_name'], 'required'],
			[['provision_type'], 'integer'],
			[['name', 'short_name'], 'string', 'max' => 255],
			[['name'], 'unique'],
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
			'provision_type' => 'Prowizja',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::className(), ['type_id' => 'id']);
	}

	public function getStages() {
		return $this->hasMany(IssueStage::className(), ['id' => 'stage_id'])
			->viaTable('{{%issue_stage_type}}', ['type_id' => 'id']);
	}

	public function getProvision(): Provision {
		if ($this->provision === null) {
			$this->provision = new Provision($this->provision_type);
		}
		return $this->provision;
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public function __toString(): string {
		return $this->name;
	}
}
