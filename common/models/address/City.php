<?php

namespace common\models\address;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "miasta".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wojewodztwo_id
 * @property integer $powiat_id
 *
 *
 * @property Province $powiatRel
 */
class City extends ActiveRecord {

	public const NOT_EXIST_NAME = 'Nieznany';

	private static $notExist;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'miasta';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['wojewodztwo_id', 'powiat_id', 'name'], 'required'],
			[['id', 'wojewodztwo_id', 'powiat_id'], 'integer'],
			[['name'], 'string', 'max' => 31],
			[['wojewodztwo_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['wojewodztwo_id' => 'id']],
			[['powiat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['powiat_id' => 'id']],

		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Miejscowość',
			'wojewodztwo_id' => 'Wojewodztwo',
			'powiat_id' => 'Powiat',
		];
	}

	//to DropDown selectList
	public static function getCitiesList($wojID, $powID) {
		$cities = static::find()->where("wojewodztwo_id=$wojID AND powiat_id=$powID")->all();
		$out = [];
		foreach ($cities as $city) {
			$out[] = ['id' => $city['id'], 'name' => $city['name']];
		}
		return [
			'out' => $out,
			'selected' => $cities[0]['id'] ?? '',
		];
	}

	public function getState(){
		return $this->hasOne(State::class, ['id' => 'wojewodztwo_id']);
	}

	public function getWojewodztwo() {
		return $this->hasOne(State::class, ['id' => 'wojewodztwo_id']);
	}

	public function getPowiatRel() {
		return $this->hasOne(Province::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id']);
	}

	public function getProvince(){
		return $this->hasOne(Province::class, ['id' => 'powiat_id', 'wojewodztwo_id' => 'wojewodztwo_id']);
	}

	public function __toString(): string {
		return $this->name;
	}

	public static function getNotExistCity(): self {
		if (empty(static::$notExist)) {
			$city = static::findOne(['name' => static::NOT_EXIST_NAME]);
			if ($city === null) {
				$city = static::createNotExistCity();
			}
			static::$notExist = $city;
		}
		return static::$notExist;
	}

	private static function createNotExistCity(): self {
		$city = new static([
			'name' => static::NOT_EXIST_NAME,
			'powiat_id' => Province::find()->one()->id,
			'wojewodztwo_id' => State::find()->one()->id,
		]);
		$city->save();
		return $city;
	}
}
