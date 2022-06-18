<?php

namespace common\modules\lead\models\entities;

use yii\base\Model;
use yii\helpers\Json;

class LeadMarketOptions extends Model {

	public bool $visibleRegion = false;
	public bool $visibleDistrict = false;
	public bool $visibleCommune = false;
	public bool $visibleCity = false;
	public bool $visibleAddressDetails = false;

	/**
	 * {@inheritDoc}
	 */
	public function rules(): array {
		return [
			[['visibleRegion', 'visibleDistrict', 'visibleCommune', 'visibleCity', 'visibleAddressDetails'], 'boolean'],
		];
	}

	public function toString(): string {
		$data = $this->toArray();
		ksort($data);
		return Json::encode($data);
	}
}
