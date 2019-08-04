<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "powiaty".
 *
 * @property integer $id
 * @property integer $wojewodztwo_id
 * @property string $name
 */
class Powiat extends ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'powiaty';
	}

	public function beforeSave($insert) {
		if ($insert) {
			$this->id = $this->getNewID();
		}
		return parent::beforeSave($insert);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'wojewodztwo_id', 'name'], 'required'],
			[['id', 'wojewodztwo_id'], 'integer'],
			[['name'], 'string', 'max' => 25],
			[['wojewodztwo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Wojewodztwa::class, 'targetAttribute' => ['wojewodztwo_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'wojewodztwo_id' => 'Wojewodztwo',
			'name' => 'Nazwa',
		];
	}

	public static function getPowiatListId($cat_id) {
		return self::find()->where(['wojewodztwo_id' => $cat_id])->asArray()->all();
	}

	public function getWojewodztwo() {
		return $this->hasOne(Wojewodztwa::class, ['id' => 'wojewodztwo_id']);
	}

	private function getNewID(): int {
		$newID = (int) self::find()->where(['wojewodztwo_id' => $this->wojewodztwo_id])->max('id');
		$newID++;
		return $newID;
	}

	public function __toString(): string {
		return $this->name;
	}

}
