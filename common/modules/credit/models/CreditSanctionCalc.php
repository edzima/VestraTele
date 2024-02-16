<?php

namespace common\modules\credit\models;

use common\modules\credit\components\InterestRate;
use DateTime;
use Exception;
use MathPHP\Finance;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\di\Instance;

class CreditSanctionCalc extends Model {

	public const INSTALLMENTS_EQUAL = 'equal';
	public const INSTALLMENTS_DECLINING = 'declining';

	public string $installmentsType = self::INSTALLMENTS_EQUAL;
	public string $interestRateType = InterestRate::INTEREST_RATE_FIXED;

	public ?float $sumCredit = null;
	public ?float $provision = null;

	public ?float $yearNominalPercent = null;

	public ?int $periods = null;

	public string $firstInstallmentAt = '';

	public string $dateAt = '';

	/**
	 * @var string[]|InterestRate|string
	 */
	public $interestRateComponent = [
		'class' => InterestRate::class,
	];

	public function init() {
		$this->interestRateComponent = Instance::ensure($this->interestRateComponent, InterestRate::class);
		parent::init();
	}

	public function rules(): array {
		return [
			[
				[
					'sumCredit', 'firstInstallmentAt', 'yearNominalPercent', 'periods', 'dateAt',
					'installmentsType', 'interestRateType',
				], 'required',
			],
			[['interestRateType', 'installmentsType'], 'string'],
			[['sumCredit', 'yearNominalPercent', 'provision', 'periods',], 'number', 'min' => 0],
		];
	}

	public function attributeLabels(): array {
		return [
			'sumCredit' => Yii::t('credit', 'Sum Credit'),
			'yearNominalPercent' => Yii::t('credit', '%'),
			'firstInstallmentAt' => Yii::t('credit', 'First installment At'),
			'periods' => Yii::t('credit', 'Installments'),
			'dateAt' => Yii::t('credit', 'Analyze At'),
			'provision' => Yii::t('credit', 'Provision'),
		];
	}

	public function getInterestPaid(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if ($this->installmentIsPaid($part, $date)) {
				$sum += $part->interestPart;
			}
		}
		return $sum;
	}

	public function getInterestToPay(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if (!$this->installmentIsPaid($part, $date)) {
				$sum += $part->interestPart;
			}
		}
		return $sum;
	}

	public function getInterestTotal(): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			$sum += $part->interestPart;
		}
		return $sum;
	}

	public function getCapitalPaid(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if ($this->installmentIsPaid($part, $date)) {
				$sum += $part->capitalValue;
			}
		}
		return $sum;
	}

	public function getCapitalToPay(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if (!$this->installmentIsPaid($part, $date)) {
				$sum += $part->capitalValue;
			}
		}
		return $sum;
	}

	public function getCapitalTotal(): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			$sum += $part->capitalValue;
		}
		return $sum;
	}

	public function installmentIsPaid(CreditLoanInstallment $installment, string $date = null): bool {
		return strtotime($installment->date) < strtotime($date ?: $this->dateAt);
	}

	/**
	 * @var CreditLoanInstallment[]
	 */
	private array $installments = [];

	public function generateInstallments(): void {
		$this->installments = [];
		for ($period = 1; $period <= $this->periods; $period++) {
			$debt = $period === 1 ? $this->sumCredit : $this->installments[$period - 1]->debt;
			$this->installments[$period] = $this->generateInstallment($period, $debt);
		}
	}

	public function generateInstallment(int $period, float $debt = null): CreditLoanInstallment {
		$installment = $this->createInstallment();
		if ($debt === null) {
			Yii::error('debt is null');
			if ($period === 1) {
				$debt = $this->sumCredit;
			} else {
				$debt = $this->generateInstallment($period - 1)->debt;
			}
		}
		$installment->debt = $debt;
		$installment->period = $period;
		$installment->date = $this->getInstalmentDateAt($period);
		$rate = $this->interestRateComponent
			->getInterestRate($installment->date, $this->interestRateType, $this->yearNominalPercent);
		$installment->interestRate = $rate / 100;
		$installment->calculate($this->periods, $this->sumCredit);
		$installment->debt -= $installment->capitalValue;

		return $installment;
	}

	public function createInstallment() {
		switch ($this->installmentsType) {
			case static::INSTALLMENTS_EQUAL:
				return new EqualCreditLoanInstallment();
			case static::INSTALLMENTS_DECLINING:
				return new DecliningCreditLoanInstallment();
		}
		throw new InvalidConfigException('Invalid installments type.');
	}

	/**
	 * @param bool $refresh
	 * @return CreditLoanInstallment[]
	 * @throws Exception
	 */
	public function getLoanInstallments(bool $refresh = false): array {
		if ($refresh || empty($this->installments)) {
			$this->installments = [];
			$dateAt = new DateTime($this->firstInstallmentAt);
			$debt = $this->sumCredit;
			$percent = $this->yearNominalPercent / 100;
			for ($i = 1; $i <= $this->periods; $i++) {
				if ($i > 1) {
					$dateAt = $dateAt->modify('+1 month');
				}

				$installment = new CreditLoanInstallment();
				$installment->period = $i;
				$installment->date = $dateAt->format('Y-m-d');
				$percent = $this->interestRateComponent->getInterestRate($installment->date, $this->interestRateType, $this->yearNominalPercent);
				$percent /= 100;
				$installment->interestRate = $percent;
				$installment->capitalValue = Finance::ppmt(
						$percent / 12,
						$i,
						$this->periods,
						$this->sumCredit,
						0
					) * -1;

				$installment->interestPart = Finance::ipmt(
						$percent / 12,
						$i,
						$this->periods,
						$this->sumCredit,
						0
					) * -1;
				$installment->debt = $debt;
				$debt -= $installment->capitalValue;

				$this->installments[$installment->period] = $installment;
			}
		}

		return $this->installments;
	}

	protected function getInstalmentDateAt(int $period): string {
		$dateTime = new DateTime($this->firstInstallmentAt);
		if ($period > 1) {
			$dateTime = $dateTime->modify("+$period month");
		}
		return $dateTime->format('Y-m-d');
	}

	public static function getInstallmentsTypes(): array {
		return [
			static::INSTALLMENTS_EQUAL => Yii::t('credit', 'Equal'),
			static::INSTALLMENTS_DECLINING => Yii::t('credit', 'Declining'),
		];
	}

	public static function getInterestRateNames(): array {
		return InterestRate::getInterestRateNames();
	}

}
