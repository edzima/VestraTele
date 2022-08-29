<?php

namespace common\modules\lead\models\entities;

use Yii;
use yii\base\Model;
use yii\helpers\Json;

class LeadMarketOptions extends Model {

	public const VISIBLE_ADDRESS_REGION = 1;
	public const VISIBLE_ADDRESS_REGION_AND_DISTRICT = 2;
	public const VISIBLE_ADDRESS_REGION_AND_DISTRICT_WITH_COMMUNE = 3;
	public const VISIBLE_ADDRESS_CITY = 4;

	public bool $visibleAddressDetails = false;

	public int $visibleArea = self::VISIBLE_ADDRESS_REGION_AND_DISTRICT;

	public function getVisibleAreaName(): string {
		return static::visibleAreaNames()[$this->visibleArea];
	}

	public static function visibleAreaNames(): array {
		return [
			static::VISIBLE_ADDRESS_REGION => Yii::t('address', 'Region'),
			static::VISIBLE_ADDRESS_REGION_AND_DISTRICT => Yii::t('address', 'Region') . ' & ' . Yii::t('address', 'District'),
			static::VISIBLE_ADDRESS_REGION_AND_DISTRICT_WITH_COMMUNE => Yii::t('address', 'Region')
				. ' & ' . Yii::t('address', 'District')
				. ' & ' . Yii::t('address', 'Commune'),
			static::VISIBLE_ADDRESS_CITY => Yii::t('address', 'City'),
		];
	}

	public function hasAddressVisible(): bool {
		return $this->visibleArea > 0
			|| $this->visibleAddressDetails;
	}

	/**
	 * {@inheritDoc}
	 */
	public function rules(): array {
		return [
			[['visibleAddressDetails'], 'boolean'],
			['visibleArea', 'in', 'range' => array_keys(static::visibleAreaNames())],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function attributeLabels(): array {
		return [
			'visibleArea' => Yii::t('lead', 'Visible Area'),
			'visibleAreaName' => Yii::t('lead', 'Visible Area'),
			'visibleAddressDetails' => Yii::t('lead', 'Visible Address Details'),
		];
	}

	public function toJson(): string {
		$data = $this->toArray();
		ksort($data);
		return Json::encode($data);
	}
}
