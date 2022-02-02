<?php

namespace common\tests\unit\provision;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueCost;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;
use common\models\provision\SettlementUserProvisionsForm;
use common\tests\unit\Unit;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;

class SettlementUserProvisionsFormTest extends Unit {

	private SettlementUserProvisionsForm $model;

	private IssueSettlement $settlement;

	private string $value = '1230';
	private string $vat = '23';

	public function _before(): void {
		parent::_before();
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::cost(true),
			ProvisionFixtureHelper::issueType(),
			ProvisionFixtureHelper::user()
		));
	}

	public function testEmpty(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->giveForm();
		$this->tester->assertFalse($this->model->validate());
		$this->tester->assertSame('Type cannot be blank.', $this->model->getFirstError('typeId'));
	}

	public function testNotExistedIssueUserType(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->giveForm(IssueUser::TYPE_VICTIM);
		});
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->giveForm('not-existed-issue-user-type');
		});
	}

	public function testTypesForAgent(): void {
		$this->tester->wantToTest('Agent administrative type.');
		$this->giveSettlement(IssueSettlement::TYPE_ADMINISTRATIVE);
		$this->giveForm(IssueUser::TYPE_AGENT);
		$this->tester->assertNotEmpty($this->model->getTypes());
		$this->tester->assertArrayHasKey(3, $this->model->getTypes());

		$this->tester->wantToTest('Agent honorarium type.');
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->giveForm(IssueUser::TYPE_AGENT);
		$this->tester->assertNotEmpty($this->model->getTypes());
		$this->tester->assertArrayHasKey(1, $this->model->getTypes());

		$this->tester->wantToTest('Agent lawyer type.');
		$this->giveSettlement(IssueSettlement::TYPE_LAWYER);
		$this->giveForm();
		$this->tester->assertEmpty($this->model->getTypes());
	}

	public function testProvisionWithoutPays(): void {
		$this->giveSettlement(IssueSettlement::TYPE_ADMINISTRATIVE);
		$this->giveForm();
		$this->assertEmpty($this->model->getPaysValues());
	}

	public function testHonorariumProvisionWithOnePayWithoutCosts(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->givePays(1);
		$this->giveForm();
		$pays = $this->model->getPaysValues();
		$this->assertCount(1, $pays);
		$pay = reset($pays);
		$this->assertTrue($this->getNettoValue()->equals($pay));
	}

	public function testHonorariumProvisionsWithOnePayWithCostSmallerThanPay(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->givePays(1);
		$costValue = new Decimal(500);
		$this->giveCost($costValue->toFixed(2));
		$this->giveForm();
		$pays = $this->model->getPaysValues();
		$this->assertCount(1, $pays);
		$pay = reset($pays);
		$this->assertTrue($this->getNettoValue()->sub($costValue)->equals($pay));
	}

	public function testHonorariumProvisionsWithTeleInstallmentCost(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->givePays(1);
		$this->giveCost(100, true, [
			'type' => IssueCost::TYPE_INSTALLMENT,
			'user_id' => $this->settlement->getIssueModel()->tele->id,
		]);
		$this->giveForm();
		$pays = $this->model->getPaysValues();
		$this->assertCount(1, $pays);
		$pay = reset($pays);
		$this->assertTrue($this->getNettoValue()->equals($pay));
	}

	public function testHonorariumProvisionsWithOnePayWithCostGreaterThanPay(): void {
		$this->giveSettlement(IssueSettlement::TYPE_HONORARIUM);
		$this->givePays(1);
		$costValue = new Decimal(1300);
		$this->giveCost($costValue->toFixed(2));
		$this->giveForm();
		$pays = $this->model->getPaysValues();
		$this->assertEmpty($pays);
	}

	private function giveSettlement(string $type) {
		$this->settlement = IssuePayCalculation::findOne(
			$this->tester->haveRecord(IssuePayCalculation::class, [
				'value' => $this->value,
				'type' => $type,
				'owner_id' => SettlementFixtureHelper::OWNER_JOHN,
				'issue_id' => 1,
			])
		);
	}

	private function givePays(int $count = 1) {
		if ($count) {
			if ($count === 1) {
				$this->tester->haveRecord(IssuePay::class, [
					'value' => $this->getValue()->toFixed(2),
					'calculation_id' => $this->settlement->getId(),
					'vat' => $this->getVAT()->toFixed(2),
				]);
			} else {
				$value = $this->getValue();
				for ($i = 0; $i < $count; $i++) {
					$this->tester->haveRecord(IssuePay::class, [
						'value' => $value->div($count)->toFixed(2),
						'calculation_id' => $this->settlement->getId(),
						'vat' => $this->getVAT()->toFixed(2),
					]);
				}
			}
		}
	}

	/**
	 * @param IssuePayCalculation $calculation
	 * @param string $issueUserType
	 * @throws InvalidConfigException
	 */
	private function giveForm(string $issueUserType = IssueUser::TYPE_AGENT): void {
		$this->model = new SettlementUserProvisionsForm($this->settlement, $issueUserType);
	}

	private function giveCost(string $value, bool $link = true, array $attributes = []) {
		if (!isset($attributes['type'])) {
			$attributes['type'] = IssueCost::TYPE_OFFICE;
		}
		$attributes['value'] = $value;
		$attributes['issue_id'] = $this->settlement->getIssueId();
		$costId = $this->tester->haveRecord(IssueCost::class, $attributes);
		if ($link) {
			$this->settlement->linkCosts([$costId]);
		}
	}

	private function getNettoValue(): Decimal {
		return Yii::$app->tax->netto($this->getValue(), $this->getVAT());
	}

	private function getValue(): Decimal {
		return new Decimal($this->value);
	}

	private function getVAT(): Decimal {
		return new Decimal($this->vat);
	}

}
