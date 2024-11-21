<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\tests\unit\Unit;
use Decimal\Decimal;

class ProvisionUserTest extends Unit {

	public function _fixtures(): array {
		return array_merge(
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::user(),
		);
	}

	public function testCreateFromBaseTypeAsConstAndSecondaryAsPercent() {
		$primary = $this->getType(false, 1000);
		$primary->save();
		$secondary = $this->getType(true, 50);
		$secondary->setBaseTypeId($primary->id);
		$secondary->save();
		$primaryModel = new ProvisionUser();
		$primaryModel->type_id = $primary->id;
		$primaryModel->value = 2000;

		$this->tester->assertTrue($primaryModel->generateProvision()->equals(2000));

		$secondaryModel = new ProvisionUser();
		$secondaryModel->type_id = $secondary->id;
		$secondaryModel->value = 40;

		$this->tester->assertTrue($secondaryModel->generateProvision($primaryModel->generateProvision())->equals(800));
	}

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
		codecept_debug($generate);
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

	public function testPercentProvisionWithConstBaseType(): void {
		$model = new ProvisionUser();
		$model->setType($this->getType(false, 1000));

		$fromBase = ProvisionUser::createFromBaseType($model, $this->getType(true, 50));
		codecept_debug($fromBase->value);
	}

	private function getType(bool $isPercentage, string $value = '100'): ProvisionType {
		$model = new ProvisionType();
		$model->value = $value;
		$model->is_percentage = $isPercentage;
		return $model;
	}
}
