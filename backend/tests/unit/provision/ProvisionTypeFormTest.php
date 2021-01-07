<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionTypeForm;
use backend\tests\fixtures\ProvisionFixtureHelper;
use backend\tests\unit\Unit;
use common\models\issue\IssuePayCalculation;
use common\models\provision\ProvisionType;

class ProvisionTypeFormTest extends Unit {

	protected const DEFAULT_NAME = 'some_name';
	protected const DEFAULT_VALUE = 25;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(ProvisionFixtureHelper::typesFixtures());
	}

	public function testEmpty(): void {
		$model = new ProvisionTypeForm();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name cannot be blank.', $model->getFirstError('name'));
		$this->tester->assertSame('Provision value cannot be blank.', $model->getFirstError('value'));
	}

	public function testPercentage(): void {
		$model = $this->createModel(true);
		$model->save();
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(ProvisionType::class, [
			'name' => static::DEFAULT_NAME,
			'value' => static::DEFAULT_VALUE,
			'is_percentage' => true,
		]);
	}

	public function testPercentageGreaterThanHundred(): void {
		$model = $this->createModel(true, ['value' => 101]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Provision value must be no greater than 100.', $model->getFirstError('value'));
	}

	public function testPercentageAsHundred(): void {
		$model = $this->createModel(true, ['value' => 100]);
		$this->tester->assertTrue($model->save());
	}

	public function testPercentageAsZero(): void {
		$model = $this->createModel(true, ['value' => 0]);
		$this->tester->assertTrue($model->save());
	}

	public function testPercentageAsNegative(): void {
		$model = $this->createModel(true, ['value' => -1]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Provision value must be no less than 0.', $model->getFirstError('value'));
	}

	public function testRandomPercentage(): void {
		$model = $this->createModel(true, ['value' => random_int(0, 100)]);
		$this->tester->assertTrue($model->save());
	}

	public function testNotPercentage(): void {
		$model = $this->createModel(false);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(ProvisionType::class, [
			'name' => static::DEFAULT_NAME,
			'value' => static::DEFAULT_VALUE,
			'is_percentage' => false,
		]);
	}

	public function testNotPercentageAsNegative(): void {
		$model = $this->createModel(false, ['value' => -1]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Provision value must be no less than 0.', $model->getFirstError('value'));
	}

	public function testNotPercentageAsGreatherThan100(): void {
		$model = $this->createModel(false, ['value' => random_int(100, 10000)]);
		$this->tester->assertTrue($model->save());
	}

	public function testNotPercentageAsGreatherThan1000(): void {
		$model = $this->createModel(false, ['value' => 10001]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Provision value must be no greater than 10000.', $model->getFirstError('value'));
	}

	public function testOneRole(): void {
		$model = $this->createModel();
		$model->roles = ['lawyer'];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertContains('lawyer', $type->getRoles());
	}

	public function testFewRoles(): void {
		$model = $this->createModel();
		$model->roles = ['lawyer', 'agent'];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertContains('lawyer', $type->getRoles());
		$this->tester->assertContains('agent', $type->getRoles());
	}

	public function testEmptyRoles(): void {
		$model = $this->createModel();
		$model->roles = [];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertEmpty($type->getRoles());
	}

	public function testNotExistedRole(): void {
		$model = $this->createModel();
		$model->roles = ['not-existed-roles'];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Roles is invalid.', $model->getFirstError('roles'));
	}

	public function testEmptyIssueTypes(): void {
		$model = $this->createModel();
		$model->issueTypesIds = [];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertEmpty($type->getIssueTypesIds());
	}

	public function testSingleIssueType(): void {
		$model = $this->createModel();
		$model->issueTypesIds = [1];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);

		$this->tester->assertContains(1, $type->getIssueTypesIds());
	}

	public function testFewIssueTypes(): void {
		$model = $this->createModel();
		$model->issueTypesIds = [1, 2, 3];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(1, $type->getIssueTypesIds());
		$this->tester->assertContains(2, $type->getIssueTypesIds());
		$this->tester->assertContains(2, $type->getIssueTypesIds());
	}

	public function testNotExistedIssueTypes(): void {
		$model = $this->createModel();
		$model->issueTypesIds = [10];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Issue Types is invalid.', $model->getFirstError('issueTypesIds'));
	}

	public function testEmptyCalculationTypes(): void {
		$model = $this->createModel();
		$model->calculationTypes = [];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertEmpty($type->getCalculationTypes());
	}

	public function testSingleCalculationType(): void {
		$model = $this->createModel();
		$model->calculationTypes = [IssuePayCalculation::TYPE_ADMINISTRATIVE];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(IssuePayCalculation::TYPE_ADMINISTRATIVE, $type->getCalculationTypes());
	}

	public function testFewCalculationTypes(): void {
		$model = $this->createModel();
		$model->calculationTypes = [IssuePayCalculation::TYPE_ADMINISTRATIVE, IssuePayCalculation::TYPE_LAWYER];
		$this->tester->assertTrue($model->save());
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(IssuePayCalculation::TYPE_ADMINISTRATIVE, $type->getCalculationTypes());
		$this->tester->assertContains(IssuePayCalculation::TYPE_LAWYER, $type->getCalculationTypes());
	}

	public function testNotExistedCalculationType(): void {
		$model = $this->createModel();
		$model->calculationTypes = ['not-existed-calculation-type'];
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Calculation Types is invalid.', $model->getFirstError('calculationTypes'));
	}

	protected function createModel(bool $isPercentage = true, array $config = []): ProvisionTypeForm {
		$config['is_percentage'] = $isPercentage;
		if (!isset($config['name'])) {
			$config['name'] = static::DEFAULT_NAME;
		}
		if (!isset($config['value'])) {
			$config['value'] = static::DEFAULT_VALUE;
		}
		return new ProvisionTypeForm($config);
	}

	protected function grabModel(array $attributes = []): ?ProvisionType {
		if (!isset($attributes['name'])) {
			$attributes['name'] = static::DEFAULT_NAME;
		}
		if (!isset($attributes['value'])) {
			$attributes['value'] = static::DEFAULT_VALUE;
		}
		return $this->tester->grabRecord(ProvisionType::class, $attributes);
	}

}
