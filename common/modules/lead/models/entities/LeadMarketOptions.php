<?php

namespace common\modules\lead\models\entities;

use Yii;
use yii\base\Model;
use yii\helpers\Json;

class LeadMarketOptions extends Model {

	public bool $visibleRegion = true;
	public bool $visibleDistrict = true;
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

	/**
	 * {@inheritDoc}
	 */
	public function attributeLabels(): array {
		return [
			'visibleRegion' => Yii::t('lead', 'Visible Region'),
			'visibleDistrict' => Yii::t('lead', 'Visible District'),
			'visibleCommune' => Yii::t('lead', 'Visible Commune'),
			'visibleCity' => Yii::t('lead', 'Visible City'),
			'visibleAddressDetails' => Yii::t('lead', 'Visible Address Details'),
		];
	}

	public function booleanTrueAttributeLabels(): array {
		$attributes = [];
		foreach ($this->getAttributes() as $attribute => $value) {
			if (is_bool($value) && $value) {
				$attributes[] = $this->getAttributeLabel($attribute);
			}
		}
		return $attributes;
	}

	public function toJson(): string {
		$data = $this->toArray();
		ksort($data);
		return Json::encode($data);
	}
}
