<?php

namespace common\tests\unit\credit;

use common\modules\credit\components\InterestRate;
use common\modules\credit\models\CreditSanctionCalc;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class CreditSanctionCalcTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_CAPITAL_VALUE = 120000.0;
	private const DEFAULT_PERIODS = 60;
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
		$this->model->generateInstallments();
		$this->tester->assertCount(static::DEFAULT_PERIODS, $this->model->getLoanInstallments());
	}

	public function testEqualInstallmentsWithFixedRate() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRateType = InterestRate::INTEREST_RATE_FIXED;
		$this->model->installmentsType = CreditSanctionCalc::INSTALLMENTS_EQUAL;
		$this->thenSuccessValidate();
		$this->model->generateInstallments();
		$instalments = $this->model->getLoanInstallments();
		$this->tester->assertCount(static::DEFAULT_PERIODS, $instalments);
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

	public function testDecliningWibor() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRateType = InterestRate::INTEREST_RATE_WIBOR_3M;
		$this->model->installmentsType = CreditSanctionCalc::INSTALLMENTS_DECLINING;
		$this->model->generateInstallments();
		$this->assertCapitalSum();
		$installments = $this->model->getLoanInstallments();
		$first = reset($installments);
		$this->tester->assertSame(754.0, $first->interestPart);
		$this->tester->assertSame(2000.0, $first->capitalValue);
		$this->tester->assertSame(2754.0, $first->getValue());
		foreach ($installments as $installment) {
			$this->tester->assertSame(2000.0, $installment->capitalValue);
		}
		$last = end($installments);
		$this->tester->assertSame(18.08, round($last->interestPart, 2));
		$this->tester->assertSame(2000.0, $last->capitalValue);
		$this->tester->assertSame(2018.08, round($last->getValue(), 2));
	}

	private function assertCapitalSum(float $value = self::DEFAULT_CAPITAL_VALUE): void {
		$capitalValue = 0;
		foreach ($this->model->getLoanInstallments() as $installment) {
			$capitalValue += $installment->capitalValue;
		}
		codecept_debug($value - $capitalValue);
		$this->tester->assertSame($value, $capitalValue);
	}

	public function testEqualWibor() {
		$this->giveModel();
		$this->loadDefaultValues();
		$this->model->interestRateType = InterestRate::INTEREST_RATE_WIBOR_3M;
		$this->model->installmentsType = CreditSanctionCalc::INSTALLMENTS_EQUAL;
		$this->model->generateInstallments();

		$installments = $this->model->getLoanInstallments();
		$first = reset($installments);
		$this->tester->assertSame(754.0, round($first->interestPart, 2));
		$this->tester->assertSame(1652.84, round($first->capitalValue, 2));
		$this->tester->assertSame(2406.84, round($first->getValue(), 2));
	}

	private function loadDefaultValues(): void {
		$this->model->sumCredit = static::DEFAULT_CAPITAL_VALUE;
		$this->model->provision = 2000;
		$this->model->periods = static::DEFAULT_PERIODS;
		$this->model->interestRatePercent = 5;
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
