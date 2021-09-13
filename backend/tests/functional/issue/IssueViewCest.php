<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\IssueController;
use backend\tests\Step\Functional\CostIssueManager;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;

class IssueViewCest {

	/** @see IssueController::actionView() */
	public const ROUTE = '/issue/issue/view';

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
		$I->dontSeeLink('Costs');
		$I->dontSeeLink('Create settlement');
		$I->dontSeeLink('Create summon');
	}

	public function checkNoteLink(IssueManager $I): void {
		$I->assignNotePermission();
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkStageLink(IssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Change Stage');
		$I->click('Change Stage');
		$I->seeInCurrentUrl(IssueStageChangeCest::ROUTE);
	}

	public function checkCostLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Costs');
		$I->click('Costs');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCreateSettlementLink(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create settlement');
		$I->click('Create settlement');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCreateSummonLink(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create summon');
		$I->click('Create summon');
		$I->seeResponseCodeIsSuccessful();
	}

	protected function goToIssuePage(IssueManager $I, string $issueIndex = '0'): Issue {
		/** @var Issue $model */
		$model = $I->grabFixture(IssueFixtureHelper::ISSUE, $issueIndex);
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		return $model;
	}

}
