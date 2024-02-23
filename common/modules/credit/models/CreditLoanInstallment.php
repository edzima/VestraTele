<?php

namespace common\modules\credit\models;

use Yii;
use yii\base\Model;

class CreditLoanInstallment extends Model {

	public float $interestRate;

	public int $period;
	public string $date;
	public float $debt;

	public float $capitalValue;
	public $capitalCumulatively;

	public float $interestPart;

	public $interesstCumulative;

	public function attributeLabels(): array {
		return [
			'capitalValue' => Yii::t('credit', 'Capital value'),
			'interestPart' => Yii::t('credit', 'Interest part'),
			'debt' => Yii::t('credit', 'Debt'),
			'period' => Yii::t('credit', 'Period'),
			'date' => Yii::t('credit', 'Installment date'),
			'interestRate' => Yii::t('credit', 'Interest rate'),
			'value' => Yii::t('credit', 'Installment value'),
		];
	}

	public function getValue(): float {
		return $this->capitalValue + $this->interestPart;
	}

	public function calculate(int $periods, float $sumCredit): void {

	}

}



