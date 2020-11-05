<?php

namespace backend\tests\functional\issue;

use backend\tests\fixtures\IssueFixtureHelper;
use backend\tests\Step\Functional\CostIssueManager;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\issue\CostFixture;
use common\models\issue\Issue;

class IssueCostCest {

	public const ROUTE_INDEX = '/issue/cost/index';
	public const ROUTE_ISSUE = '/issue/cost/issue';
	public const ROUTE_VIEW = '/issue/cost/view';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Costs');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Costs');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsCostManager(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Costs');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Costs');
	}

	public function checkIndex(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Costs');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('VAT (%)');
		$I->seeInGridHeader('Date at');
		$I->seeInGridHeader('Created at');
		$I->seeInGridHeader('Updated at');
	}

	public function checkIssue(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		/* @var Issue $issue */
		$issue = $I->grabFixture('issue', 0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Costs: ' . $issue->longId);
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('VAT (%)');
		$I->seeInGridHeader('Date at');
		$I->seeInGridHeader('Created at');
		$I->seeInGridHeader('Updated at');

		$I->seeLink('Create');
		$I->click('Create');
		$I->see('Create cost: ' . $issue->longId);
	}

	public function checkArchivedIssue(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::fixtures());
		/* @var Issue $issue */
		$issue = $I->grabFixture('issue', 1);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->seeResponseCodeIs(404);
	}

	public function checkViewPage(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(IssueFixtureHelper::fixtures(), [
			'cost' => [
				'class' => CostFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/cost.php',
			],
		]));
		$I->amOnPage([static::ROUTE_VIEW, 'id' => 1]);
		$I->see('Purchase of receivables');
		$I->see('600.00');
		$I->see('23,00%');
	}

}
