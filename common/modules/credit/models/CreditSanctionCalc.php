<?php

namespace common\modules\credit\models;

use common\modules\credit\components\InterestRate;
use DateTime;
use Exception;
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
	public $provision;
	public $insurance;

	public $interestRatePercent = 0;

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
					'sumCredit', 'firstInstallmentAt', 'periods', 'dateAt',
					'installmentsType', 'interestRateType',
				], 'required',
			],
			[
				['interestRatePercent'], 'required',
				'when' => function (): bool {
					return $this->interestRateType === InterestRate::INTEREST_RATE_FIXED;
				},
				'enableClientValidation' => false,
			],
			[['interestRateType', 'installmentsType'], 'string'],
			[['sumCredit', 'interestRatePercent', 'provision', 'periods', 'insurance'], 'number', 'min' => 0],
		];
	}

	public function attributeLabels(): array {
		return [
			'sumCredit' => Yii::t('credit', 'Sum Credit'),
			'interestRatePercent' => $this->interestRateAttributeLabel(),
			'firstInstallmentAt' => Yii::t('credit', 'First installment At'),
			'periods' => Yii::t('credit', 'Installments'),
			'dateAt' => Yii::t('credit', 'Analyze At'),
			'provision' => Yii::t('credit', 'Provision'),
			'interestsPaid' => Yii::t('credit', 'Interests paid'),
			'interestsToPay' => Yii::t('credit', 'Interests to pay'),
			'interestsTotal' => Yii::t('credit', 'Interests total'),
			'insurance' => Yii::t('credit', 'Insurance'),
		];
	}

	public function interestRateAttributeLabel(): string {
		return InterestRate::interestRatePercentNames()[$this->interestRateType];
	}

	public function getInterestsPaid(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if ($this->installmentIsPaid($part, $date)) {
				$sum += $part->interestPart;
			}
		}
		return $sum;
	}

	public function getInterestsToPay(string $date = null): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			if (!$this->installmentIsPaid($part, $date)) {
				$sum += $part->interestPart;
			}
		}
		return $sum;
	}

	public function getInterestsTotal(): float {
		$parts = $this->getLoanInstallments();
		$sum = 0;
		foreach ($parts as $part) {
			$sum += $part->interestPart;
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
			->getInterestRate($installment->date, $this->interestRateType, $this->interestRatePercent);
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
	public function getLoanInstallments(): array {
		return $this->installments;
	}

	protected function getInstalmentDateAt(int $period): string {
		$dateTime = new DateTime($this->firstInstallmentAt);
		if ($period > 1) {
			$period--;
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

	public const  EXCLUDES_INTEREST_RATE = [
		InterestRate::INTEREST_RATE_REFERENCE_RATE,
	];

	public static function getInterestRateNames(): array {
		$names = InterestRate::getInterestRateNames();
		foreach (static::EXCLUDES_INTEREST_RATE as $interestRate) {
			unset($names[$interestRate]);
		}
		return $names;
	}

}
