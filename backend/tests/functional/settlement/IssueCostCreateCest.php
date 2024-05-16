<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CostController;
use backend\tests\Step\Functional\CostIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueCost;

class IssueCostCreateCest {

	/** @see CostController::actionCreate() */
	public const ROUTE = '/settlement/cost/create';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::cost(false)
		);
	}

	public function _before(CostIssueManager $I): void {
		$I->amLoggedIn();
	}

	protected function issue(CostIssueManager $I) {
		/* @var Issue $issue */
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$I->amOnPage([static::ROUTE, 'id' => $issue->id]);
	}

	public function createWithoutIssue(CostIssueManager $I) {
		$I->amOnPage([static::ROUTE]);
		$I->selectOption('Type', IssueCost::TYPE_PURCHASE_OF_RECEIVABLES);
		$I->fillField('Value', 100);
		$I->fillField('VAT (%)', 23);
		$I->click('Save');
		$I->seeLink('Update');
	}

	public function checkDefaultSave(CostIssueManager $I): void {
		$this->issue($I);
		$I->seeInField('Date at', date('Y-m-d'));
		$I->click('Save');
		$I->seeValidationError('Value cannot be blank.');
	}

	public function checkEmptyDate(CostIssueManager $I): void {
		$this->issue($I);
		$I->fillField('Date at', '');
		$I->click('Save');
		$I->seeValidationError('Date at cannot be blank.');
	}

	public function checkValidCreate(CostIssueManager $I): void {
		$this->issue($I);
		$I->selectOption('Type', IssueCost::TYPE_PURCHASE_OF_RECEIVABLES);
		$I->fillField('Value', 100);
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
