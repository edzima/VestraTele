<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\Bookkeeper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;

class CalculationUpdateCest {

	public const ROUTE = '/settlement/calculation/update';

	public function _before(Bookkeeper $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkUpdateValue(Bookkeeper $I): void {
		$calculation = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		/** @var IssuePayCalculation $calculation */
		$I->amOnPage([static::ROUTE, 'id' => $calculation->id]);
		$I->see('Update settlement: ' . $calculation->getTypeName());
		$I->seeInField('Value with VAT', '1230');
		$I->fillField('Value with VAT', 2460);
		$I->click('Save');
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'value' => 2460,
		]);
		$I->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 1230,
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 2460,
		]);
	}

	public function checkUpdateValueForManyPays(Bookkeeper $I): void {
		$calculation = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'many-pays');
		/** @var IssuePayCalculation $calculation */
		$I->amOnPage([static::ROUTE, 'id' => $calculation->id]);
		$I->seeInField('Value with VAT', '1230');
		$I->fillField('Value with VAT', 2460);
		$I->click('Save');
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'value' => 2460,
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 615,
			'pay_at' => '2020-01-01',
		]);
		$I->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 1230,
		]);
	}

	public function checkChangeType(Bookkeeper $I): void {
		$calculation = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		/** @var IssuePayCalculation $calculation */
		$I->amOnPage([static::ROUTE, 'id' => $calculation->id]);
		$I->seeOptionIsSelected('#calculationform-type', IssuePayCalculation::getTypesNames()[IssuePayCalculation::TYPE_ADMINISTRATIVE]);
		$I->selectOption('#calculationform-type', IssuePayCalculation::TYPE_HONORARIUM);
		$I->click('Save');
		$I->dontSeeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
		]);
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'type' => IssuePayCalculation::TYPE_HONORARIUM,
		]);
	}

	public function checkUpdateDeadlineAt(Bookkeeper $I): void {
		$calculation = $I->grabFixture(IssueFixtureHelper::CALCULATION, 'not-payed');
		/** @var IssuePayCalculation $calculation */
		$I->amOnPage([static::ROUTE, 'id' => $calculation->id]);
		$I->see('Update settlement: ' . $calculation->getTypeName());
		$I->seeInField('Deadline at', '2019-01-01');
		$I->fillField('Deadline at', '2021-02-01');
		$I->click('Save');
		$I->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'deadline_at' => '2019-01-01',
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'deadline_at' => '2021-02-01',
		]);
	}

}
