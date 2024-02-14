<?php

namespace common\tests\unit\credit;

use common\modules\credit\models\CreditLoanInstallment;
use common\modules\credit\models\CreditSanctionCalc;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class CreditSanctionCalcTest extends Unit {

	use UnitModelTrait;

	private CreditSanctionCalc $model;

	public function testEmpty() {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Sum Credit cannot be blank.', 'sumCredit');
		$this->thenSeeError('Installments cannot be blank.', 'periods');
		$this->thenSeeError('Analyze At cannot be blank.', 'dateAt');
		$this->thenSeeError('Credit At cannot be blank.', 'creditAt');
	}

	public function testDefaultValues() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->thenSuccessValidate();
		$this->tester->assertSame(CreditLoanInstallment::INTEREST_RATE_FIXED, $this->model->interestRate);
		$this->tester->assertSame(CreditLoanInstallment::INSTALLMENTS_EQUAL, $this->model->installmentsType);
		$this->tester->assertCount(60, $this->model->getLoanInstallments());
	}

	public function testEqualInstallmentsWithFixedRate() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRate = CreditLoanInstallment::INTEREST_RATE_FIXED;
		$this->model->installmentsType = CreditLoanInstallment::INSTALLMENTS_EQUAL;
		$this->thenSuccessValidate();
		$instalments = $this->model->getLoanInstallments();
		$first = reset($instalments);
		$this->tester->assertSame(500.00, $first->interestPart);
		foreach ($instalments as $installment) {
			$this->tester->assertSame(2264.55, $installment->getValue());
		}
	}

	private function giveModel(array $config = []) {
		$this->model = new CreditSanctionCalc($config);
	}

	private function loadDefaultValues(): void {
		$this->model->sumCredit = 120000;
		$this->model->provision = 2000;
		$this->model->periods = 60;
		$this->model->dateAt = '2024-01-01';
		$this->model->creditAt = '2022-01-01';
		$this->model->yearNominalPercent = 5;
	}

	public function getModel(): CreditSanctionCalc {
		return $this->model;
	}
}
