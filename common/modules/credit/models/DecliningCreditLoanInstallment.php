<?php

namespace common\modules\credit\models;

class DecliningCreditLoanInstallment extends CreditLoanInstallment {

	public function calculate(int $periods, float $sumCredit): void {
		$this->capitalValue = $this->getCapitalValue($periods, $sumCredit);
		$this->interestPart = $this->getInterestPart();
	}

	public function getCapitalValue(int $periods, float $sumCredit): float {
		//	 * @todo why check debt * rate /12 > 0
		return $sumCredit / $periods;
	}

	public function getInterestPart(): float {
		return $this->interestRate * $this->debt / 12;
	}
}
