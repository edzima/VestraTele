<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-05
 * Time: 12:27
 */

namespace common\modules\address\widgets;

use common\models\address\Address;
use common\models\address\City;
use common\models\address\SubProvince;
use common\models\address\Province;
use common\models\address\State;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AddressFormWidget extends Widget {

	public $legend = 'Adres';

	public $state;
	public $stateAdd = '/address/state/create';
	public $province;
	public $provinceAdd = '/address/province/create';
	public $subProvince;
	public $subProvinceAdd = '/address/sub-province/create';

	public $city;
	public $cityAdd = '/address/city/create';
	public $street;
	public $cityCode;

	public $copyOptions = [];

	public $form;
	/** @var Model */
	public $model;

	public function init() {
		if ($this->model instanceof Address) {
			$this->state = 'stateId';
			$this->province = 'provinceId';
			if ($this->subProvince !== false) {
				$this->subProvince = 'subProvinceId';
			}
			if ($this->city !== false) {
				$this->city = 'cityId';
			}
			if ($this->cityCode !== false) {
				$this->cityCode = 'cityCode';
			}
			if ($this->street !== false) {
				$this->street = 'street';
			}
		}
		if (!empty($this->copyOptions)) {
			if (!isset($this->copyOptions['data-selector'])) {
				throw new InvalidConfigException('$data-selector must be set in $copyOptions.');
			}
			if (!isset($this->copyOptions['inputs'])) {
				throw new InvalidConfigException('$inputs must be set in $copyOptions.');
			}
		}
		parent::init();
	}

	public function run() {
		return $this->render('address', [
			'id' => $this->getId(),
			'legend' => $this->legend,
			'form' => $this->form,
			'model' => $this->model,
			'state' => $this->state,
			'stateAdd' => $this->stateAdd,
			'province' => $this->province,
			'provinceAdd' => $this->provinceAdd,
			'subProvince' => $this->subProvince,
			'subProvinceAdd' => $this->subProvinceAdd,
			'city' => $this->city,
			'cityAdd' => $this->cityAdd,
			'street' => $this->street,
			'cityCode' => $this->cityCode,
			'copyOptions' => $this->copyOptions,
		]);
	}

	public static function getStates(): array {
		return ArrayHelper::map(State::find()
			->all(), 'id', 'name');
	}

	public static function getProvinces(int $stateID): array {
		return ArrayHelper::map(Province::find()
			->where(['wojewodztwo_id' => $stateID])
			->all(), 'id', 'name');
	}

	public static function getSubprovinces(int $stateId, int $provinceId): array {
		return ArrayHelper::map(SubProvince::find()->where([
			'WOJ' => $stateId,
			'POW' => $provinceId,
		])->all(), 'id', 'name');
	}

	public static function getCities(int $stateId, int $provinceId): array {
		return ArrayHelper::map(City::find()
			->where(['wojewodztwo_id' => $stateId, 'powiat_id' => $provinceId])
			->all(), 'id', 'name');
	}

}
