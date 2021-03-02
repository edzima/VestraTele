<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;

class IssueProvisionTypeTest extends ProvisionTypeTest {

	public function fixtures(): array {
		return ProvisionFixtureHelper::issueType();
	}

	public function testIssueUserType(): void {
		$type = $this->grabType('agent-percent-25');
		$this->tester->assertTrue($type->isForIssueUser(IssueUser::TYPE_AGENT));
		$this->tester->assertFalse($type->isForIssueUser(IssueUser::TYPE_LAWYER));
		$this->tester->assertFalse($type->isForIssueUser('not-existed-issue-type'));
		$this->tester->assertSame('agent', $type->getIssueUserTypeName());

		$type = $this->grabType('tele-percent-5');
		$this->tester->assertTrue($type->isForIssueUser(IssueUser::TYPE_TELEMARKETER));
		$this->tester->assertFalse($type->isForIssueUser(IssueUser::TYPE_AGENT));
		$this->tester->assertFalse($type->isForIssueUser('not-existed-issue-type'));
		$this->tester->assertSame('telemarketer', $type->getIssueUserTypeName());
	}

	public function testIssueType(): void {
		$this->tester->haveFixtures(IssueFixtureHelper::types());
		$type = new IssueProvisionType();

		$this->tester->wantToTest('Empty type');
		$type->setIssueTypesIds([]);
		foreach (IssueType::getTypesIds() as $typeId) {
			$this->tester->assertTrue($type->isForIssueType($typeId));
		}
		$this->tester->assertSame('All', $type->getIssueTypesNames());

		$this->tester->wantToTest('Single type');
		$type->setIssueTypesIds([1]);
		$this->tester->assertTrue($type->isForIssueType(1));
		$this->tester->assertFalse($type->isForIssueType(2));
		$this->tester->assertFalse($type->isForIssueType(3));
		$this->tester->assertSame('Accident', $type->getIssueTypesNames());

		$this->tester->wantToTest('Multiply type');
		$type->setIssueTypesIds([1, 2]);
		$this->tester->assertTrue($type->isForIssueType(1));
		$this->tester->assertTrue($type->isForIssueType(2));
		$this->tester->assertFalse($type->isForIssueType(3));
		$this->tester->assertSame('Accident, Benefits - administrative proceedings', $type->getIssueTypesNames());
	}

	public function testCalculationTypesNames(): void {
		$type = new IssueProvisionType();

		$this->tester->wantToTest('Empty type');
		$type->setCalculationTypes([]);
		foreach (IssuePayCalculation::getTypesNames() as $key => $name) {
			$this->tester->assertTrue($type->isForCalculationType($key));
		}
		$this->tester->assertSame('All', $type->getCalculationTypesNames());

		$this->tester->wantToTest('One type');
		$type->setCalculationTypes([IssuePayCalculation::TYPE_ADMINISTRATIVE]);
		foreach (IssuePayCalculation::getTypesNames() as $key => $name) {
			if ($key === IssuePayCalculation::TYPE_ADMINISTRATIVE) {
				$this->tester->assertTrue($type->isForCalculationType($key));
			} else {
				$this->tester->assertFalse($type->isForCalculationType($key));
			}
		}
		$this->tester->assertSame('Administrative', $type->getCalculationTypesNames());

		$this->tester->wantToTest('Multiple types');
		$type->setCalculationTypes([IssuePayCalculation::TYPE_ADMINISTRATIVE, IssuePayCalculation::TYPE_LAWYER]);
		$this->tester->assertTrue($type->isForCalculationType(IssuePayCalculation::TYPE_ADMINISTRATIVE));
		$this->tester->assertTrue($type->isForCalculationType(IssuePayCalculation::TYPE_LAWYER));
		$this->tester->assertFalse($type->isForCalculationType(IssuePayCalculation::TYPE_HONORARIUM));
		$this->tester->assertSame('Administrative, Lawyer', $type->getCalculationTypesNames());
	}

	protected function grabType(string $index): IssueProvisionType {
		return $this->tester->grabFixture(ProvisionFixtureHelper::TYPE, $index);
	}
}
