<?php

namespace common\tests\unit\settlement;

use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use console\models\DemandForPayment;

class DemandForPaymentTest extends Unit {

	use UnitModelTrait;

	private DemandForPayment $model;

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Which cannot be blank.', 'which');
		$this->tester->assertNull($this->model->delayedDays);
	}

	public function getModel(): DemandForPayment {
		return $this->model;
	}

	private function giveModel(array $config = []) {
		$this->model = new DemandForPayment($config);
	}
}
