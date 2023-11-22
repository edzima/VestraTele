<?php

namespace common\components\postal\models;

use Yii;

class Shipment {

	public const STATUS_OK = 0;
	public const STATUS_THERE_ARE_OTHER_WITH_THIS_NUMBER = 1;
	public const STATUS_EXIST_BUT_WITHOUT_EVENTS_FOR_DATE_RANGE = 2;
	public const STATUS_NOT_EXIST = -1;
	public const STATUS_INVALID_NUMBER = -2;
	public const STATUS_OTHER_ERROR = -99;
	public ?ShipmentDetails $danePrzesylki;
	public string $numer;
	public int $status;

	public function isOk(): bool {
		return $this->status === static::STATUS_OK;
	}

	public function getStatusName(): string {
		return static::statusNames()[$this->status];
	}

	public static function statusNames(): array {
		return [
			static::STATUS_OK => Yii::t('poczta-polska', 'OK'),
			static::STATUS_THERE_ARE_OTHER_WITH_THIS_NUMBER => Yii::t('poczta-polska', 'There are other with this number'),
			static::STATUS_EXIST_BUT_WITHOUT_EVENTS_FOR_DATE_RANGE => Yii::t('poczta-polska', 'Exist but without events for date range'),
			static::STATUS_NOT_EXIST => Yii::t('poczta-polska', 'Not exist'),
			static::STATUS_INVALID_NUMBER => Yii::t('poczta-polska', 'Invalid number'),
			static::STATUS_OTHER_ERROR => Yii::t('poczta-polska', 'Other error'),
		];
	}
}
