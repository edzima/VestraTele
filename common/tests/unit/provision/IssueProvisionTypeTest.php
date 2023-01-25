<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use yii\base\InvalidCallException;

class IssueProvisionTypeTest extends ProvisionTypeTest {

	public function fixtures(): array {
		return ProvisionFixtureHelper::issueType();
	}

	public function testRequiredIssueUserTypesWithoutIssueAndTypes(): void {
		$type = new IssueProvisionType();
		$type->setIssueRequiredUserTypes([
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]);
		$this->tester->expectThrowable(InvalidCallException::class, function () use ($type) {
			$type->hasRequiredIssueUserTypes(null, []);
		});
	}

	public function testExcludedIssueUserTypesWithoutSet(): void {
		$type = new IssueProvisionType();
		$this->tester->assertFalse($type->hasExcludedIssueUserTypes(null, [
			IssueUser::TYPE_AGENT,
		]));

		$type->setIssueExcludedUserTypes([]);
		$this->tester->assertFalse($type->hasExcludedIssueUserTypes(null, [
			IssueUser::TYPE_AGENT,
		]));
	}

	public function testExcludedIssueUserTypesWithoutIssue(): void {
		$type = new IssueProvisionType();
		$type->setIssueExcludedUserTypes([
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]);

		$this->tester->assertFalse($type->hasExcludedIssueUserTypes(null, [
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_AGENT,
		]));

		$this->tester->assertTrue($type->hasExcludedIssueUserTypes(null, [
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]));

		$this->tester->assertTrue($type->hasExcludedIssueUserTypes(null, [
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]));
	}

	public function testRequiredIssueUserTypesWithoutRequired(): void {
		$type = new IssueProvisionType();
		$this->tester->assertTrue($type->hasRequiredIssueUserTypes(null, [
			IssueUser::TYPE_AGENT,
		]));

		$type->setIssueRequiredUserTypes([]);
		$this->tester->assertTrue($type->hasRequiredIssueUserTypes(null, [
			IssueUser::TYPE_AGENT,
		]));
	}

	public function testRequiredIssueUserTypesWithoutIssue(): void {
		$type = new IssueProvisionType();
		$type->setIssueRequiredUserTypes([
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]);

		$this->tester->wantToTest('Has all types that required.');
		$this->tester->assertTrue($type->hasRequiredIssueUserTypes(null, [
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]));

		$this->tester->wantToTest('Has all types that required with one more.');
		$this->tester->assertTrue($type->hasRequiredIssueUserTypes(null, [
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
			IssueUser::TYPE_RECOMMENDING,
		]));

		$this->tester->wantToTest('Has all types that required without one.');

		$this->tester->assertFalse($type->hasRequiredIssueUserTypes(null, [
			IssueUser::TYPE_AGENT,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_TELEMARKETER,
		]));
	}

	public function testIssueRequiredUserTypesNames(): void {
		$type = new IssueProvisionType();
		$this->tester->assertSame('(not set)', $type->getIssueRequiredUserTypesNames());
		$type->setIssueRequiredUserTypes([]);
		$this->tester->assertSame('(not set)', $type->getIssueRequiredUserTypesNames());
		$type->setIssueRequiredUserTypes([IssueUser::TYPE_AGENT]);
		$this->tester->assertSame('agent', $type->getIssueRequiredUserTypesNames());
		$type->setIssueRequiredUserTypes([IssueUser::TYPE_AGENT, IssueUser::TYPE_LAWYER]);
		$this->tester->assertSame('agent, lawyer', $type->getIssueRequiredUserTypesNames());
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
		$type->setSettlementTypes([]);
		foreach (IssuePayCalculation::getTypesNames() as $key => $name) {
			$this->tester->assertTrue($type->isForSettlementType($key));
		}
		$this->tester->assertSame('All', $type->getSettlementTypesNames());

		$this->tester->wantToTest('One type');
		$type->setSettlementTypes([IssuePayCalculation::TYPE_ADMINISTRATIVE]);
		foreach (IssuePayCalculation::getTypesNames() as $key => $name) {
			if ($key === IssuePayCalculation::TYPE_ADMINISTRATIVE) {
				$this->tester->assertTrue($type->isForSettlementType($key));
			} else {
				$this->tester->assertFalse($type->isForSettlementType($key));
			}
		}
		$this->tester->assertSame('Administrative', $type->getSettlementTypesNames());

		$this->tester->wantToTest('Multiple types');
		$type->setSettlementTypes([IssuePayCalculation::TYPE_ADMINISTRATIVE, IssuePayCalculation::TYPE_LAWYER]);
		$this->tester->assertTrue($type->isForSettlementType(IssuePayCalculation::TYPE_ADMINISTRATIVE));
		$this->tester->assertTrue($type->isForSettlementType(IssuePayCalculation::TYPE_LAWYER));
		$this->tester->assertFalse($type->isForSettlementType(IssuePayCalculation::TYPE_HONORARIUM));
		$this->tester->assertSame('Administrative, Lawyer', $type->getSettlementTypesNames());
	}

	protected function grabType(string $index): IssueProvisionType {
		return $this->tester->grabFixture(ProvisionFixtureHelper::TYPE, $index);
	}
}
