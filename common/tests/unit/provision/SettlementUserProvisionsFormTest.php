<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\SettlementUserProvisionsForm;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class SettlementUserProvisionsFormTest extends Unit {

	private SettlementUserProvisionsForm $model;

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(true, codecept_data_dir() . 'provision/'),
			ProvisionFixtureHelper::issueType(),
			ProvisionFixtureHelper::user()
		));
	}

	public function testNotExistedIssueUserType(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->giveForm($this->grabCalculation('without-telemarketer'), IssueUser::TYPE_TELEMARKETER);
		});
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->giveForm($this->grabCalculation('without-telemarketer'), 'not-existed-issue-user-type');
		});
	}

	public function testTypesForAgent(): void {
		$this->tester->wantToTest('Agent administrative type.');
		$this->giveForm($this->grabCalculation('administrative'));
		$this->tester->assertNotEmpty($this->model->getTypes());
		$this->tester->assertArrayHasKey(3, $this->model->getTypes());

		$this->tester->wantToTest('Agent honorarium type.');
		$this->giveForm($this->grabCalculation('honorarium'));
		$this->tester->assertNotEmpty($this->model->getTypes());
		$this->tester->assertArrayHasKey(1, $this->model->getTypes());

		$this->tester->wantToTest('Agent lawyer type.');
		$this->giveForm($this->grabCalculation('lawyer'));
		$this->tester->assertEmpty($this->model->getTypes());
	}

	/**
	 * @param IssuePayCalculation $calculation
	 * @param string $issueUserType
	 * @throws InvalidConfigException
	 */
	private function giveForm(IssuePayCalculation $calculation, string $issueUserType = IssueUser::TYPE_AGENT): void {
		$this->model = new SettlementUserProvisionsForm($calculation, $issueUserType);
	}

	private function grabCalculation(string $index): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}

}
