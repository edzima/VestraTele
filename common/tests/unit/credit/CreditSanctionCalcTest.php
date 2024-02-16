<?php

namespace common\tests\unit\credit;

use common\modules\credit\components\InterestRate;
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
		$this->thenSeeError('First installment At cannot be blank.', 'firstInstallmentAt');
	}

	public function testDefaultValues() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->thenSuccessValidate();
		$this->tester->assertSame(InterestRate::INTEREST_RATE_FIXED, $this->model->interestRateType);
		$this->tester->assertSame(CreditSanctionCalc::INSTALLMENTS_EQUAL, $this->model->installmentsType);
		$this->tester->assertCount(60, $this->model->getLoanInstallments());
	}

	public function testEqualInstallmentsWithFixedRate() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRateType = InterestRate::INTEREST_RATE_FIXED;
		$this->model->installmentsType = CreditSanctionCalc::INSTALLMENTS_EQUAL;
		$this->thenSuccessValidate();
		$instalments = $this->model->getLoanInstallments();
		$first = reset($instalments);
		$this->tester->assertSame(500.00, $first->interestPart);
		foreach ($instalments as $installment) {
			$this->tester->assertSame(2264.55, round($installment->getValue(), 2));
		}
	}

	public function testDecliningInstallmentsWithFixedRate() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRateType = InterestRate::INTEREST_RATE_FIXED;
		$this->model->installmentsType = CreditSanctionCalc::INSTALLMENTS_DECLINING;
		$this->thenSuccessValidate();
		$first = $this->model->generateInstallment(1);
		$this->tester->assertSame(2000.0, $first->capitalValue);
		$this->tester->assertSame(500.0, $first->interestPart);
		$second = $this->model->generateInstallment(2);
		$this->tester->assertSame(2000.0, $second->capitalValue);
	}

	private function loadDefaultValues(): void {
		$this->model->sumCredit = 120000;
		$this->model->provision = 2000;
		$this->model->periods = 60;
		$this->model->yearNominalPercent = 5;
		$this->model->dateAt = '2024-01-01';
		$this->model->firstInstallmentAt = '2022-01-01';
	}

	private function giveModel(array $config = []) {
		$this->model = new CreditSanctionCalc($config);
	}

	public function getModel(): CreditSanctionCalc {
		return $this->model;
	}
}
