<?php

namespace common\modules\credit\components;

use common\modules\credit\models\CreditLoanInstallment;
use Yii;
use yii\base\Component;
use yii\di\Instance;

class InterestRate extends Component implements InterestRateInterface {

	public const INTEREST_RATE_FIXED = 'fixed';
	public const INTEREST_RATE_WIBOR_3M = 'wibor_3m';
	public const INTEREST_RATE_REFERENCE_RATE = 'referenceNBP';

	public $model = [
		'class' => CreditLoanInstallment::class,
	];

	public $wibor = [
		'class' => WiborArchiveComponent::class,
	];

	public $referenceRate = [
		'class' => ReferenceRateNBPComponent::class,
	];

	public function getInterestRate(string $date, string $interestType = null, float $baseRate = 0): float {
		$interestRate = $baseRate;
		switch ($interestType) {
			case static::INTEREST_RATE_WIBOR_3M:
				$wibor = $this->getWibor()->getInterestRate($date);
				if ($wibor) {
					$interestRate += $wibor;
				} else {
					Yii::warning('not found wibor rate for date: ' . $date);
				}
				break;
			case static::INTEREST_RATE_REFERENCE_RATE:
				$reference = $this->getReferenceRateNBP()->getInterestRate($date);
				if ($reference) {
					$interestRate += $reference;
				} else {
					Yii::warning('not found reference rate for date: ' . $date);
				}
				break;
		}
		return $interestRate;
	}

	public function getWiborInterestRate(string $date): ?float {
		return $this->getWibor()->getInterestRate($date);
	}

	public function getReferenceRateInterestRate(string $date): ?float {
		return $this->getReferenceRateNBP()->getInterestRate($date);
	}

	protected function getWibor(): InterestRateInterface {
		if (!$this->wibor instanceof InterestRateInterface) {
			$this->wibor = Instance::ensure($this->wibor, InterestRateInterface::class);
		}
		return $this->wibor;
	}

	protected function getReferenceRateNBP(): InterestRateInterface {
		if (!$this->referenceRate instanceof InterestRateInterface) {
			$this->referenceRate = Instance::ensure($this->referenceRate, InterestRateInterface::class);
		}
		return $this->referenceRate;
	}

	public static function getInterestRateNames(): array {
		return [
			static::INTEREST_RATE_FIXED => Yii::t('credit', 'Fixed interest rate'),
			static::INTEREST_RATE_WIBOR_3M => Yii::t('credit', 'Wibor 3M'),
			static::INTEREST_RATE_REFERENCE_RATE => Yii::t('credit', 'Reference Rate'),
		];
	}

}
