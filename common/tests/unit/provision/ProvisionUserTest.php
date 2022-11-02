<?php

namespace common\tests\unit\provision;

use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\tests\unit\Unit;
use Decimal\Decimal;

class ProvisionUserTest extends Unit {

	public function testProvisionForPercentTypeWithoutBaseType(): void {
		$model = new ProvisionUser();
		$type = new ProvisionType();
		$type->is_percentage = true;
		$model->setType($this->getType(true));
		$model->value = 50;
		$generate = $model->generateProvision(new Decimal(100));
		$this->tester->assertTrue($generate->equals(50));
	}

	public function testProvisionForPercentBaseType(): void {
		$model = new ProvisionUser();
		$model->setType($this->getType(true));
		$model->value = 50;

		$fromBase = ProvisionUser::createFromBaseType($model, $this->getType(true, 50));
		$generate = $fromBase->generateProvision(new Decimal(100));
		$this->tester->assertTrue($generate->equals(25));
	}

	public function testProvisionForNotPercentTypeWithoutBaseType(): void {
		$model = new ProvisionUser();
		$model->setType($this->getType(false, 10));
		$model->value = 50;
		$generate = $model->generateProvision(new Decimal(10));
		$this->tester->assertTrue($generate->equals(50));
	}

	public function testProvisionForNotPercentBaseType(): void {
		$model = new ProvisionUser();
		$model->setType($this->getType(false));
		$model->value = 50;
		$fromBase = ProvisionUser::createFromBaseType($model, $this->getType(false, 50));
		$generate = $fromBase->generateProvision(new Decimal(10));
		$this->tester->assertTrue($generate->equals(100));
	}

	private function getType(bool $isPercentage, string $value = '100'): ProvisionType {
		$model = new ProvisionType();
		$model->value = $value;
		$model->is_percentage = $isPercentage;
		return $model;
	}
}
