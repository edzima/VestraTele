<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CreateCalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;

class CalculationCreateCest {

	public const ROUTE = '/settlement/calculation/create';

	public function _before(CreateCalculationIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
		));
		$I->amLoggedIn();
	}

	public function checkCreatePage(CreateCalculationIssueManager $I): void {

		/** @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE, 'id' => $issue->id]);
		$I->see('Create calculation for: ' . $issue->longId);
		$I->see($issue->customer->getFullName());
		$I->see($issue->type->name);
		$I->see($issue->stage->name);
	}

	public function checkSubmitEmpty(CreateCalculationIssueManager $I): void {
		$I->amOnPage([static::ROUTE, 'id' => 1]);
		$I->click('Save');
		$I->seeValidationError('Value with VAT cannot be blank.');
	}

	public function checkValid(CreateCalculationIssueManager $I): void {
		$I->amOnPage([static::ROUTE, 'id' => 1]);
		$I->dontSee('Problem status');
		$I->fillField('Value with VAT', 123);
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->seeLink('Update');
		$model = $I->grabRecord(IssuePayCalculation::class, [
			'issue_id' => 1,
			'value' => 123,
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $model->id,
			'value' => 123,
		]);
	}
}
