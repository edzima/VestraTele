<?php

namespace common\modules\credit\models;

use MathPHP\Finance;

class EqualCreditLoanInstallment extends CreditLoanInstallment {

	public function calculate(int $periods, float $sumCredit): void {
		$this->capitalValue = $this->getCapitalValue($periods, $sumCredit);
		$this->interestPart = $this->getInterestPart($periods, $sumCredit);
	}

	public function getInterestPart(int $periods, float $sumCredit): float {
		return Finance::ipmt(
				$this->interestRate / 12,
				$this->period,
				$periods,
				$sumCredit,
				0
			) * -1;
	}

	public function getCapitalValue(int $periods, float $sumCredit): float {
		return Finance::ppmt(
				$this->interestRate / 12,
				$this->period,
				$periods,
				$sumCredit,
				0
			) * -1;
	}

}
