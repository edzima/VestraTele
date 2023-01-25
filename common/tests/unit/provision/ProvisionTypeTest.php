<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\provision\ProvisionType;
use common\tests\unit\Unit;
use Yii;

class ProvisionTypeTest extends Unit {

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures($this->fixtures());
	}

	public function fixtures(): array {
		return ProvisionFixtureHelper::type();
	}

	public function testPercentFormattedValue(): void {
		$type = new ProvisionType();
		$type->is_percentage = true;
		$type->value = 20;
		$this->assertSame('20,00%', $type->getFormattedValue());
	}

	public function testPercentValueLabel(): void {
		$type = new ProvisionType();
		$type->is_percentage = true;
		$this->tester->assertSame('Provision (%)', $type->getAttributeLabel('formattedValue'));
	}

	public function testNotPercentFormattedValue(): void {
		$type = new ProvisionType();
		$type->is_percentage = false;
		$type->value = 20;
		$this->assertSame(Yii::$app->formatter->asCurrency(20), $type->getFormattedValue());
	}

	public function testNotPercentValueLabel(): void {
		$type = new ProvisionType();
		$type->is_percentage = false;
		$this->tester->assertSame('Provision (' . Yii::$app->formatter->getCurrencySymbol() . ')', $type->getAttributeLabel('formattedValue'));
	}

	public function testBaseTypeId(): void {
		$type = new ProvisionType();
		$this->tester->assertNull($type->getBaseTypeId());
		$type->setBaseTypeId(null);
		$this->tester->assertNull($type->getBaseTypeId());
		$type->setBaseTypeId(1);
		$this->tester->assertSame(1, $type->getBaseTypeId());
		$type->setBaseTypeId(null);
		$this->tester->assertNull($type->getBaseTypeId());
	}

	protected function grabType(string $index): ProvisionType {
		return $this->tester->grabFixture(ProvisionFixtureHelper::TYPE, $index);
	}
}
