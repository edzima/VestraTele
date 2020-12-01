<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\CalculationIssueManager;
use backend\tests\Step\Functional\CostIssueManager;
use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;

class IssueViewCest {

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function checkPage(IssueManager $I): void {
		$I->amLoggedIn();
		$model = $this->goToIssuePage($I);
		$I->seeInTitle($model->longId);
		$I->see($model->customer->getFullName());
		$I->see($model->type->name);
		$I->see($model->stage->name);
		$I->see($model->lawyer->getFullName());
	}

	public function checkLinksAsManager(IssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Update');
		$I->seeLink('Issue users');
		$I->dontSeeLink('Costs');
		$I->dontSeeLink('Calculations');
	}

	public function checkNoteLink(IssueManager $I): void {
		$I->assignNotePermission();
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCostLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Costs');
		$I->click('Costs');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCalculationsLink(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Calculations');
		$I->click('Calculations');
		$I->seeResponseCodeIsSuccessful();
	}

	protected function goToIssuePage(IssueManager $I, string $issueIndex = '0'): Issue {
		/** @var Issue $model */
		$model = $I->grabFixture('issue', $issueIndex);
		$I->amOnPage(['/issue/issue/view', 'id' => $model->id]);
		return $model;
	}

}
