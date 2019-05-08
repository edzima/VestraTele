<?php

namespace common\models;


/**
 * This is the model class for table "terc".
 *
 * @property integer id
 * @property integer $WOJ
 * @property integer $POW
 * @property integer $GMI
 * @property string $name
 */
class Gmina extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'terc';
	}

	public function __toString() {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['WOJ', 'POW', 'GMI'], 'integer'],
			[['name'], 'string', 'max' => 36],
			[['WOJ'], 'exist', 'skipOnError' => true, 'targetClass' => Wojewodztwa::className(), 'targetAttribute' => ['WOJ' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'WOJ' => 'Woj',
			'POW' => 'Pow',
			'GMI' => 'Gmi',
			'name' => 'Nazwa',
		];
	}

	public static function getGminaList(int $wojID, int $powID, int $selected = null): array {

		$gminy = static::find()->where("woj=$wojID AND POW =$powID")->all();
		$out = [];
		foreach ($gminy as $gmina) {
			$out[] = ['id' => $gmina['id'], 'name' => $gmina['name']];
		}

		return [
			'out' => $out,
			'selected' => $selected !== null ? $gminy[0]['id'] : $selected,
		];
	}

}
