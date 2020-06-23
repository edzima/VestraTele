<?php

namespace common\models\address;

use yii\base\Model;

class Address extends Model {

	public $cityId;
	public $stateId;
	public $provinceId;
	public $subProvinceId;

	public $street;
	public $cityCode;

	public $requiredCity = true;
	public $uniqueCityClass;
	public $uniqueCityAttribute = 'city_id';

	public $formName;

	/** @var City */
	private $city;
	/** @var State */
	private $state;
	/** @var Province */
	private $province;
	/** @var SubProvince */
	private $subProvince;

	public $customRules = [];

	public function rules(): array {
		return array_merge($this->customRules,
			[
				['cityId', 'required', 'when' => function () { return $this->requiredCity; }, 'message' => 'Miejscowość jest obowiązkowa.'],
				[['stateId', 'provinceId', 'cityId', 'subProvinceId'], 'integer'],
				[['cityCode', 'street'], 'string'],
				['stateId', 'exist', 'targetClass' => State::class, 'targetAttribute' => ['stateId' => 'id']],
				['provinceId', 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['provinceId' => 'id']],
				['subProvinceId', 'exist', 'targetClass' => SubProvince::class, 'targetAttribute' => ['subProvinceId' => 'id']],
				['cityId', 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['cityId' => 'id']],
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

	public function formName(): string {
		if (!empty($this->formName)) {
			return $this->formName;
		}
		return parent::formName();
	}

	public function attributeLabels(): array {
		return [
			'state' => 'Województwo',
			'province' => 'Powiat',
			'subProvince' => 'Gmina',
			'city' => 'Miejscowość',
			'street' => 'Ulica',
		];
	}

	public function setCity(City $city): void {
		$this->city = $city;
		$this->cityId = $city->id;
		$this->stateId = (int) $city->wojewodztwo_id;
		$this->provinceId = (int) $city->powiat_id;
	}

	public function getCity(): ?City {
		if ($this->city === null || $this->city->id !== $this->cityId) {
			$this->city = $this->cityId ? static::findCity($this->cityId) : null;
		}
		return $this->city;
	}

	public function getState(): ?State {
		if ($this->state === null || $this->state->id !== $this->stateId) {
			$this->state = $this->stateId ? static::findState($this->stateId) : null;
		}
		return $this->state;
	}

	public function getProvince(): ?Province {
		if ($this->province === null || $this->state->id !== $this->stateId) {
			$this->province = ($this->provinceId && $this->stateId) ? static::findProvince($this->provinceId, $this->stateId) : null;
		}
		return $this->province;
	}

	public function getSubProvince(): ?SubProvince {
		if ($this->subProvince === null || $this->subProvince->id !== $this->subProvinceId) {
			$this->subProvince = $this->subProvinceId ? static::findSubProvince($this->subProvinceId) : null;
		}
		return $this->subProvince;
	}

	protected static function findState(int $id): ?State {
		return State::findOne($id);
	}

	protected static function findProvince(int $provinceId, int $stateId): ?Province {
		return Province::find()->where(['id' => $provinceId, 'wojewodztwo_id' => $stateId])->one();
	}

	protected static function findSubProvince(int $id): ?SubProvince {
		return SubProvince::findOne($id);
	}

	protected static function findCity(int $id): ?City {
		return City::findOne($id);
	}

	public static function createFromCity(City $city): self {
		$model = new static();
		$model->setCity($city);

		return $model;
	}

	public static function createFromCityId(int $cityId): self {
		$city = static::findCity($cityId);
		if ($city !== null) {
			return static::createFromCity($city);
		}
		return new static();
	}

	public function setSubProvince(SubProvince $subProvince): void {
		$this->subProvince = $subProvince;
		$this->subProvinceId = $subProvince->id;
	}

}
