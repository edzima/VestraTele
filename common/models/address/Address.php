<?php

namespace common\models\address;

use common\models\City;
use common\models\Wojewodztwa;
use yii\base\Model;

class Address extends Model {

	public $stateId;
	public $provinceId;
	public $subProvince;
	public $cityId;
	public $street;

	public $uniqueCityClass;
	public $uniqueCityAttribute = 'city_id';

	public function rules(): array {
		return array_merge([
			[['cityId'], 'required', 'message' => 'Miejscowość musi zostać wybrana'],
			[['cityId'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['cityId' => 'id']],
			[['stateId', 'provinceId', 'subProvinceId', 'cityId', 'street'], 'safe'],
		],
			!empty($this->uniqueCityClass)
				? [
				[
					'cityId',
					'unique', 'targetClass' => $this->uniqueCityClass, 'targetAttribute' => ['cityId' => $this->uniqueCityAttribute],
					'message' => 'Miejscowość musi być unikalna',
				],
			]
				: []
		);
	}

	public static function createFromCity(City $city): self {
		$model = new static();
		$model->cityId = (int) $city->id;
		$model->stateId = (int) $city->wojewodztwo_id;
		$model->provinceId = (int) $city->powiat_id;
		return $model;
	}

	public static function createFromCityId(int $cityId): self {
		$city = static::findCity($cityId);
		if ($city !== null) {
			return static::createFromCity($city);
		}
		return new static();
	}

	public function getCity(): ?City {
		return static::findCity($this->cityId);
	}

	protected static function findCity(int $id): ?City {
		return City::findOne($id);
	}

	public function getState(): ?Wojewodztwa {
		return Wojewodztwa::findOne([$this->stateId]);
	}

}