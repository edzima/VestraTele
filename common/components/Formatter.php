<?php

namespace common\components;

use common\helpers\Html;
use Decimal\Decimal;
use NumberFormatter;
use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter {

	public const FRACTION_DIGITS = 2;

	public $numberFormatterOptions = [
		NumberFormatter::MIN_FRACTION_DIGITS => self::FRACTION_DIGITS,
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

	public function asCurrency($value, $currency = null, $options = [], $textOptions = []) {
		if ($value instanceof Decimal) {
			$value = $value->toFixed(self::FRACTION_DIGITS);
		}
		return parent::asCurrency($value, $currency, $options, $textOptions);
	}

	public function asTel($value, $options = []) {
		if ($value === null) {
			return $this->nullDisplay;
		}

		return Html::telLink(Html::encode($value), $value, $options);
	}

}
