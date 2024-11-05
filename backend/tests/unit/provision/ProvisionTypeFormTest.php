<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionTypeForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionType;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class ProvisionTypeFormTest extends Unit {

	use UnitModelTrait;

	protected const DEFAULT_NAME = 'some_name';
	protected const DEFAULT_VALUE = 25;
	protected const DEFAULT_ISSUE_USER_TYPE = IssueUser::TYPE_AGENT;

	private ProvisionTypeForm $model;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
				ProvisionFixtureHelper::issueType(),
				SettlementFixtureHelper::type()
			)
		);
		$this->giveModel();
	}

	public function testEmpty(): void {
		$this->model = new ProvisionTypeForm();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Value cannot be blank.', 'value');
	}

	public function testPercentage(): void {
		$this->giveModel(true);
		$this->thenSuccessSave();
		$this->tester->seeRecord(ProvisionType::class, [
			'name' => static::DEFAULT_NAME,
			'value' => static::DEFAULT_VALUE,
			'is_percentage' => true,
		]);
	}

	public function testPercentageGreaterThanHundred(): void {
		$this->giveModel(true, ['value' => 101]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value must be no greater than 100.', 'value');
	}

	public function testPercentageAsHundred(): void {
		$this->giveModel(true, ['value' => 100]);
		$this->thenSuccessSave();
	}

	public function testPercentageAsZero(): void {
		$this->giveModel(true, ['value' => 0]);
		$this->thenSuccessSave();
	}

	public function testPercentageAsNegative(): void {
		$this->giveModel(true, ['value' => -1]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Value must be no less than 0.', 'value');
	}

	public function testRandomPercentage(): void {
		$this->giveModel(true, ['value' => random_int(0, 100)]);
		$this->thenSuccessSave();
	}

	public function testNotPercentage(): void {
		$this->giveModel(false);
		$this->thenSuccessSave();
		$this->tester->seeRecord(ProvisionType::class, [
			'name' => static::DEFAULT_NAME,
			'value' => static::DEFAULT_VALUE,
			'is_percentage' => false,
		]);
	}

	public function testNotPercentageAsNegative(): void {
		$this->giveModel(false, ['value' => -1]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Value must be no less than 0.', 'value');
	}

	public function testNotPercentageAsGreatherThan100(): void {
		$this->giveModel(false, ['value' => random_int(100, 10000)]);
		$this->thenSuccessSave();
	}

	public function testIssueRequiredUserTypesAsNull(): void {
		$model = $this->model;
		$model->issueRequiredUserTypes = null;
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertEmpty($type->getIssueRequiredUserTypes());
	}

	public function testIssueRequiredUserTypesAsEmptyArray(): void {
		$model = $this->model;
		$model->issueRequiredUserTypes = [];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertEmpty($type->getIssueRequiredUserTypes());
	}

	public function testNotExistedIssueRequiredUserTypes(): void {
		$model = $this->model;
		$model->issueRequiredUserTypes = ['not-existed-type'];
		$this->thenUnsuccessSave();
		$this->thenSeeError('Required issue user types is invalid.', 'issueRequiredUserTypes');
	}

	public function testValidIssueRequiredUserType(): void {
		$model = $this->model;
		$model->issueRequiredUserTypes = [IssueUser::TYPE_LAWYER];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertContains(IssueUser::TYPE_LAWYER, $type->getIssueRequiredUserTypes());
	}

	public function testValidIssueRequiredUserTypes(): void {
		$model = $this->model;
		$model->issueRequiredUserTypes = [IssueUser::TYPE_LAWYER, IssueUser::TYPE_AGENT];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertContains(IssueUser::TYPE_LAWYER, $type->getIssueRequiredUserTypes());
		$this->tester->assertContains(IssueUser::TYPE_AGENT, $type->getIssueRequiredUserTypes());
	}

	public function testIssueUserType(): void {
		$model = $this->model;
		$model->issueUserType = IssueUser::TYPE_LAWYER;
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertInstanceOf(ProvisionType::class, $type);
		$this->tester->assertTrue($type->isForIssueUser(IssueUser::TYPE_LAWYER));
	}

	public function testEmptyIssueUserType(): void {
		$model = $this->model;
		$model->issueUserType = null;
		$this->thenUnsuccessSave();
		$this->thenSeeError('For whom cannot be blank.', 'issueUserType');
	}

	public function testNotExistedIssueUserType(): void {
		$model = $this->model;
		$model->issueUserType = 'not-existed-type';
		$this->thenUnsuccessSave();
		$this->thenSeeError('For whom is invalid.', 'issueUserType');
	}

	public function testEmptyIssueTypes(): void {
		$model = $this->model;
		$model->issueTypesIds = [];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertEmpty($type->getIssueTypesIds());
	}

	public function testSingleIssueType(): void {
		$this->tester->haveFixtures(IssueFixtureHelper::types());
		$this->giveModel();
		$model = $this->model;
		$model->issueTypesIds = [1];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);

		$this->tester->assertContains(1, $type->getIssueTypesIds());
	}

	public function testFewIssueTypes(): void {
		$this->tester->haveFixtures(IssueFixtureHelper::types());
		$this->giveModel();
		$model = $this->model;
		$model->issueTypesIds = [1, 2, 3];

		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(1, $type->getIssueTypesIds());
		$this->tester->assertContains(2, $type->getIssueTypesIds());
		$this->tester->assertContains(2, $type->getIssueTypesIds());
	}

	public function testNotExistedIssueTypes(): void {
		$model = $this->model;
		$model->issueTypesIds = [10];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Issue Types is invalid.', 'issueTypesIds');
	}

	public function testEmptyCalculationTypes(): void {
		$model = $this->model;
		$model->settlementTypes = [];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertEmpty($type->getSettlementTypes());
	}

	public function testSingleCalculationType(): void {
		$model = $this->model;
		$model->settlementTypes = [SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE, $type->getSettlementTypes());
	}

	public function testFewCalculationTypes(): void {
		$model = $this->model;
		$model->settlementTypes = [SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE, SettlementFixtureHelper::TYPE_ID_LAWYER];
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertNotNull($type);
		$this->tester->assertContains(SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE, $type->getSettlementTypes());
		$this->tester->assertContains(SettlementFixtureHelper::TYPE_ID_LAWYER, $type->getSettlementTypes());
	}

	public function testNotExistedSettlementType(): void {
		$this->model->settlementTypes = ['not-existed-calculation-type'];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Settlement type is invalid.', 'settlementTypes');
	}

	public function testWithHierarchy(): void {
		$model = $this->model;
		$model->with_hierarchy = true;
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertTrue($type->getWithHierarchy());
	}

	public function testWithoutHierarchy(): void {
		$model = $this->model;
		$model->with_hierarchy = false;
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertFalse($type->getWithHierarchy());
	}

	public function testWithBaseTypeId(): void {
		$model = $this->model;
		$model->baseTypeId = 1;
		$this->thenSuccessSave();
		$type = $this->grabModel();
		$this->tester->assertSame(1, $type->getBaseTypeId());
	}

	protected function giveModel(bool $isPercentage = true, array $config = []): void {
		$config['is_percentage'] = $isPercentage;
		if (!isset($config['name'])) {
			$config['name'] = static::DEFAULT_NAME;
		}
		if (!isset($config['value'])) {
			$config['value'] = static::DEFAULT_VALUE;
		}
		if (!isset($config['issueUserType'])) {
			$config['issueUserType'] = static::DEFAULT_ISSUE_USER_TYPE;
		}
		$this->model = new ProvisionTypeForm($config);
	}

	protected function grabModel(array $attributes = []): ?IssueProvisionType {
		if (!isset($attributes['name'])) {
			$attributes['name'] = static::DEFAULT_NAME;
		}
		if (!isset($attributes['value'])) {
			$attributes['value'] = static::DEFAULT_VALUE;
		}
		return $this->tester->grabRecord(IssueProvisionType::class, $attributes);
	}

	public function getModel(): Model {
		return $this->model;
	}
}
