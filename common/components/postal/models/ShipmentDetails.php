<?php

namespace common\components\postal\models;

class ShipmentDetails {

	public $return;
	public string $numer;
	public string $kodRodzPrzes;
	public string $rodzPrzes;
	public ?string $dataNadania;
	public string $krajNadania;
	public string $kodKrajuPrzezn;
	public string $krajPrzezn;
	public ?float $masa;
	public ?string $format;
	public bool $zakonczonoObsluge;

	public EventsList $zdarzenia;
}
