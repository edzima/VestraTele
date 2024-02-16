<?php

namespace common\modules\credit\models;

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

	public function getValue(): float {
		return $this->capitalValue + $this->interestPart;
	}

	public function calculate(int $periods, float $sumCredit): void {

	}

}



