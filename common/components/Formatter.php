<?php

namespace common\components;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use Decimal\Decimal;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use NumberFormatter;
use yii\i18n\Formatter as BaseFormatter;
use common\models\user\User as UserModel;

class Formatter extends BaseFormatter {

	public $nullDisplay = '';
	public const FRACTION_DIGITS = 2;
	public const FRACTION_PERCENT_DIGITS = 4;

	public $numberFormatterOptions = [
		NumberFormatter::MIN_FRACTION_DIGITS => self::FRACTION_DIGITS,
	];
	public string $defaultPhoneRegion = 'PL';
	public int $defaultPhoneFormat = PhoneNumberFormat::INTERNATIONAL;

	public function asUserEmail(?UserModel $user): ?string {
		if ($user) {
			if ($user->email) {
				return Html::mailto($user->getFullName(), $user->email);
			}
			return $user->getFullName();
		}
		return $this->nullDisplay;
	}

	public function asCityCode(?string $city, ?string $code, bool $postalStrongTag = true) {
		if ($city === null && $code === null) {
			return $this->nullDisplay;
		}
		if ($city === null) {
			return Html::encode($code);
		}
		if ($code === null) {
			return Html::encode($city);
		}
		$city = Html::encode($city);
		$code = Html::encode($code);
		if ($postalStrongTag) {
			$code = Html::tag('strong', $code);
		}
		return "$city - [$code]";
	}

	public function asMonthDay($date): string {
		return $this->asDate($date, "d'-go'");
	}

	public function asPercent($value, $decimals = null, $options = [], $textOptions = []) {
		if ($value instanceof Decimal) {
			$value = $value->toFixed(self::FRACTION_PERCENT_DIGITS);
		}
		return parent::asPercent($value, $decimals, $options, $textOptions);
	}

	public function asCurrency($value, $currency = null, $options = [], $textOptions = []) {
		if ($value instanceof Decimal) {
			$value = $value->toFixed(self::FRACTION_DIGITS);
		}
		return parent::asCurrency($value, $currency, $options, $textOptions);
	}

	public function getCurrencySymbol($currencyCode = null): string {
		if ($currencyCode === null) {
			$currencyCode = $this->currencyCode;
		}
		if (empty($currencyCode)) {
			return '';
		}
		$formatter = new NumberFormatter($this->locale . '@currency=' . $currencyCode, NumberFormatter::CURRENCY);
		return $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
	}

	public function asTel($value, $options = []) {
		if ($value === null) {
			return ArrayHelper::getValue($options, 'nullDisplay', $this->nullDisplay);
		}
		$defaultRegion = ArrayHelper::remove($options, 'default_region', $this->defaultPhoneRegion);
		$format = ArrayHelper::remove($options, 'format', $this->defaultPhoneFormat);

		try {
			$phoneValue = $this->getPhoneUtil()->parse($value, $defaultRegion);
			$value = $this->getPhoneUtil()->format($phoneValue, $format);
		} catch (NumberParseException $e) {
		}

		$asLink = ArrayHelper::remove($options, 'asLink', true);
		if ($asLink) {
			return Html::telLink(Html::encode($value), $value, $options);
		}
		return Html::encode($value);
	}

	public function asPhoneDatabase($value, $options = []): ?string {
		if ($value === null) {
			return $this->nullDisplay;
		}
		$defaultRegion = ArrayHelper::remove($options, 'default_region', $this->defaultPhoneRegion);
		try {
			$phoneValue = $this->getPhoneUtil()->parse($value, $defaultRegion);
			return $this->getPhoneUtil()->format($phoneValue, PhoneNumberFormat::E164);
		} catch (NumberParseException $e) {
		}
		return null;
	}

	protected function getPhoneUtil(): PhoneNumberUtil {
		return PhoneNumberUtil::getInstance();
	}

}
