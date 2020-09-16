<?php

namespace common\formatters;

use NumberFormatter;
use yii\helpers\Html;
use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter {

	public $currencyCode = 'PLN';

	public $numberFormatterOptions = [
		NumberFormatter::MIN_FRACTION_DIGITS => 2,
	];

	public function asCityCode(?string $city, ?string $code) {
		if ($city === null && $code === null) {
			return $this->nullDisplay;
		}
		if ($city === null) {
			return Html::encode($code);
		}
		if ($code === null) {
			return Html::encode($city);
		}
		return Html::encode("$city - ($code)");
	}

	public function asMonthDay($date): string {
		return $this->asDate($date, "d'-go'");
	}

}
