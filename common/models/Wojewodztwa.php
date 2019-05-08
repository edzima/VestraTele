<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wojewodztwa".
 *
 * @property integer $id
 * @property string $name
 */
class Wojewodztwa extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'wojewodztwa';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name'], 'string', 'max' => 19],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Name',
		];
	}

	public function beforeSave($insert) {
		if ($this->isNewRecord) {
			$this->id = static::find()->max('id') + 1;
		}
		return parent::beforeSave($insert);
	}

	public function __toString() {
		return $this->name;
	}

	public static function getSelectList(): array {
		return ArrayHelper::map(static::find()->all(), 'id', 'name');
	}
}
