<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\SettlementProvisionsForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class SettlementProvisionsFormTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_CALCULATION_INDEX = 'payed';

	private SettlementProvisionsForm $form;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(true),
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::user()
		));
	}

	public function testEmpty(): void {
		$this->givenForm($this->grabCalculation());
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Agent provision can not be blank.', 'agentProvision');
	}

	public function testTypes(): void {
		$this->tester->wantToTest('Administrative calculation');
		$this->givenForm($this->grabCalculation('payed'));
		$types = $this->form->getTypes();
		$this->tester->assertCount(1, $types);

		$this->tester->wantToTest('Honorarium calculation');
		$this->givenForm($this->grabCalculation('many-pays'));
		$types = $this->form->getTypes();
		$this->tester->assertCount(2, $types);
	}

	public function testUserTypes(): void {
		$this->tester->wantToTest('Administrative calculation wuth type only for Agent');
		$this->givenForm($this->grabCalculation('payed'));
		$this->tester->assertCount(1, $this->form->getUsersTypes());
		$this->tester->assertNotEmpty($this->form->getUserTypes(IssueUser::TYPE_AGENT));
		$this->tester->assertEmpty($this->form->getUserTypes(IssueUser::TYPE_LAWYER));

		$this->tester->wantToTest('Honorarium calculation with types for Agent and Tele.');
		$this->givenForm($this->grabCalculation('many-pays'));
		$this->tester->assertCount(2, $this->form->getUsersTypes());
		$this->tester->assertNotEmpty($this->form->getUserTypes(IssueUser::TYPE_AGENT));
		$this->tester->assertNotEmpty($this->form->getUserTypes(IssueUser::TYPE_TELEMARKETER));
		$this->tester->assertEmpty($this->form->getUserTypes(IssueUser::TYPE_LAWYER));
	}

	private function givenForm(IssuePayCalculation $calculation) {
		$this->form = new SettlementProvisionsForm($calculation);
	}

	private function grabCalculation(string $index = self::DEFAULT_CALCULATION_INDEX): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}

	public function getModel(): Model {
		return $this->form;
	}
}
