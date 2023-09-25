<?php

namespace frontend\models;

use Decimal\Decimal;
use Yii;
use yii\base\Model;

//MPKK = (K x 10%) + (K x n/R x 10%)
//
//w którym poszczególne symbole oznaczają:
//
//MPKK - maksymalną wysokość pozaodsetkowych kosztów kredytu,
//
//K - całkowitą kwotę kredytu,
//
//n - okres spłaty wyrażony w dniach,
//
//R - liczbę dni w roku.
class MaxAmountOfNonInterestFinancialCosts extends Model {

	public $totalLoanAmount;
	public $months;

	private Decimal $value;

	private bool $isCalculate = false;

	public function attributeLabels(): array {
		return [
			'months' => Yii::t('frontend', 'Months'),
			'totalLoanAmount' => Yii::t('frontend', 'Total loan amount'),
		];
	}

	public function rules(): array {
		return [
			[['months', 'totalLoanAmount'], 'required'],
			[['months'], 'integer', 'min' => 1],
			[['totalLoanAmount'], 'number', 'min' => 1],
		];
	}

	public function getIsCalculate(): bool {
		return $this->isCalculate;
	}

	public function calculate(): void {
		if ($this->validate()) {
			$this->isCalculate = true;
			$days = (new Decimal($this->months))->mul('30.4375');
			$this->value = ((new Decimal($this->totalLoanAmount))->mul('0.1'))
				->add(
					(new Decimal($this->totalLoanAmount))->mul($days)->div(365)->mul('0.1')
				);
		}
	}

	public function getValueText(): string {
		return $this->value->toFixed(2);
	}
}
