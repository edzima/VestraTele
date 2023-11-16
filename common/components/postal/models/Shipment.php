<?php

namespace common\components\postal\models;

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
}
