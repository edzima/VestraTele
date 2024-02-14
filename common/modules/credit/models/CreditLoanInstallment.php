<?php

namespace common\modules\credit\models;

use common\modules\credit\components\InterestRate;
use Yii;
use yii\base\Model;

class CreditLoanInstallment extends Model {

	public const INSTALLMENTS_EQUAL = 'equal';
	public const INSTALLMENTS_DECLINING = 'declining';

	public const INTEREST_RATE_FIXED = 'fixed';
	public const INTEREST_RATE_WIBOR_3M = 'wibor_3m';
	public const INTEREST_RATE_REFERENCE_RATE = 'referenceNBP';

	public $interestRate;
	public string $installmentsType;

	public int $period;
	public string $date;
	public float $debt;

	public float $baseInterestRate;
	public $capitalValue;
	public $capitalCumulatively;

	public $interestPart;

	public $interesstCumulative;

	public $interestRateProvider = [
		'class' => InterestRate::class,
	];

	protected $value;

	public function getValue(): float {
		return round($this->capitalValue + $this->interestPart, 2);
	}

	public static function getInstallmentsTypes(): array {
		return [
			static::INSTALLMENTS_EQUAL => Yii::t('credit', 'Equal'),
			static::INSTALLMENTS_DECLINING => Yii::t('credit', 'Declining'),
		];
	}

	public static function getInterestRateNames(): array {
		return [
			static::INTEREST_RATE_FIXED => Yii::t('credit', 'Fixed interest rate'),
			static::INTEREST_RATE_WIBOR_3M => Yii::t('credit', 'Wibor 3M'),
			static::INTEREST_RATE_REFERENCE_RATE => Yii::t('credit', 'Reference Rate'),
		];
	}

}
