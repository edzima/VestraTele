<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CostIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueCost;

class IssueCostCreateCest {

	public const ROUTE = '/settlement/cost/create';

	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function _before(CostIssueManager $I): void {
		$I->amLoggedIn();
		/* @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE, 'id' => $issue->id]);
	}

	public function checkDefaultSave(CostIssueManager $I): void {
		$I->seeInField('Date at', date('Y-m-d'));
		$I->click('Save');
		$I->seeValidationError('Value with VAT cannot be blank.');
		$I->seeValidationError('VAT (%) cannot be blank.');
	}

	public function checkEmptyDate(CostIssueManager $I): void {
		$I->fillField('Date at', '');
		$I->click('Save');
		$I->seeValidationError('Date at cannot be blank.');
	}

	public function checkValidCreate(CostIssueManager $I): void {
		$I->selectOption('Type', IssueCost::TYPE_PURCHASE_OF_RECEIVABLES);
		$I->fillField('Value with VAT', 100);
		$I->fillField('VAT (%)', 23);
		$I->click('Save');
		$I->seeLink('Update');
		$I->seeRecord(IssueCost::class, [
			'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
			'value' => 100,
			'vat' => 23,
		]);

		$I->seeLink('Delete');
	}

}
