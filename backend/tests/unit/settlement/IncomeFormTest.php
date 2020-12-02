<?php

namespace backend\tests\unit\settlement;

use backend\modules\issue\models\IncomeForm;
use backend\tests\unit\Unit;

class IncomeFormTest extends Unit {

	public function testIncomeFromInt(): void {
		$model = new IncomeForm();
		$model->costs = 600;
		$model->gross = 1830;
		$this->tester->assertSame(1230, $model->income()->toInt());
	}

	public function testIncomeFromString(): void {
		$model = new IncomeForm();
		$model->costs = '600';
		$model->gross = '1830';
		$this->tester->assertSame(1230, $model->income()->toInt());
	}

	public function testNetIncome(): void {
		$model = new IncomeForm();
		$model->costs = 600;
		$model->gross = 1830;
		$model->tax = 23;
		$this->tester->assertSame(1000, $model->netIncome()->toInt());
	}

	public function testWithoutCosts(): void {
		$model = new IncomeForm();
		$model->gross = 1230;
		$model->tax = 23;
		$this->tester->assertSame(1230, $model->income()->toInt());
		$this->tester->assertSame(1000, $model->netIncome()->toInt());
	}

}
