<?php

namespace backend\modules\hint\models;

use edzima\teryt\models\District;
use edzima\teryt\models\Region;
use edzima\teryt\models\Simc;
use Yii;
use yii\base\Model;

class HintDistrictForm extends Model {

	public $region_id;
	public $district_id;
	public $commune_id;
	public $user_id;
	public $type;
	public $details;

	public function rules(): array {
		return [
			[['region_id', 'district_id', 'user_id', 'type'], 'required'],
			[['region_id', 'district_id', 'user_id', 'commune_id'], 'integer'],
			[['type', 'details'], 'string'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['user_id', 'in', 'range' => array_keys(static::getUsersNames())],
			['region_id', 'in', 'range' => array_keys(static::getRegionsNames())],
			['district_id', 'exist', 'targetClass' => District::class, 'targetAttribute' => ['region_id' => 'region_id', 'district_id' => 'district_id']],
		];
	}

	public function attributeLabels(): array {
		return array_merge(HintCityForm::instance()->attributeLabels(), [
			'region_id' => Yii::t('address', 'Region'),
			'district_id' => Yii::t('address', 'District'),
			'commune_id' => Yii::t('address', 'Commune'),
		]);
	}

	public static function getRegionsNames(): array {
		return Region::getNames();
	}

	public static function getTypesNames(): array {
		return HintCityForm::getTypesNames();
	}

	public static function getUsersNames(): array {
		return HintCityForm::getUsersNames();
	}

	public function save(bool $validate = true): int {
		if ($validate && !$this->validate()) {
			return 0;
		}
		$count = 0;
		$citiesIDs = $this->getCitiesIDs();
		foreach ($citiesIDs as $cityID) {
			$model = new HintCityForm();
			$model->city_id = $cityID;
			$model->user_id = $this->user_id;
			$model->type = $this->type;
			$model->details = $this->details;
			if ($model->save()) {
				$count++;
			}
		}
		return $count;
	}

	private function getCitiesIDs(): array {
		return Simc::find()
			->andWhere([
				'region_id' => $this->region_id,
				'district_id' => $this->district_id,
			])
			->andFilterWhere(['commune_id' => $this->commune_id])
			->andWhere('id = base_id')
			->groupBy('name')
			->select('id')
			->column();
	}

}
