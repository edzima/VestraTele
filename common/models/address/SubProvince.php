<?php

namespace common\models\address;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "terc".
 *
 * @property integer id
 * @property integer $WOJ
 * @property integer $POW
 * @property integer $GMI
 * @property string $name
 *
 * @property State $state
 * @property Province $province
 */
class SubProvince extends ActiveRecord {

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
			[['name', 'WOJ', 'POW'], 'required'],
			[['WOJ', 'POW', 'GMI'], 'integer'],
			[['name'], 'string', 'max' => 36],
			[['WOJ'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['WOJ' => 'id']],
			[['POW'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['WOJ' => 'wojewodztwo_id', 'POW' => 'id']],
			[
				'name', 'unique',
				'filter' => function (ActiveQuery $query) {
					if (!$this->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->id]]);
					}
				},
				'targetAttribute' => ['WOJ', 'POW', 'name'],
				'message' => 'Gmina: {value} już istnieje',
			],
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
			'state' => Yii::t('address', 'state'),
			'province' => Yii::t('address', 'province'),
		];
	}

	public function getState() {
		return $this->hasOne(State::class, ['id' => 'WOJ']);
	}

	public function getProvince() {
		return $this->hasOne(Province::class, ['id' => 'POW', 'wojewodztwo_id' => 'WOJ']);
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
