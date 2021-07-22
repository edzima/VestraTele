<?php

namespace backend\tests\functional\settlement;

use backend\helpers\Url;
use backend\tests\Step\Functional\CostIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\user\Worker;

class DebtCostCest {

	private const ROUTE_CREATE_DEBT = '/settlement/cost/create-debt';

	public function checkCreateLinkOnIssueCostsPageWithoutPermission(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::issue());
		$I->amOnRoute(IssueCostCest::ROUTE_ISSUE, ['id' => 1]);
		$I->seeResponseCodeIsSuccessful();
		$I->dontSeeLink('Create Debt Costs');
	}

	public function checkCreateLinkOnIssueCostsPageWithPermission(CostIssueManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(),
			SettlementFixtureHelper::cost(false)
		));
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_COST_DEBT);

		$I->amOnRoute(IssueCostCest::ROUTE_ISSUE, ['id' => 1]);
		$I->seeResponseCodeIsSuccessful();
		$I->seeLink('Create Debt Costs');
		$I->click('Create Debt Costs');
		$I->seeInCurrentUrl(Url::toRoute([static::ROUTE_CREATE_DEBT, 'issue_id' => 1]));
	}

	public function checkCreateDebt(CostIssueManager $I): void {

		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(),
			SettlementFixtureHelper::cost(false)
		));
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_COST_DEBT);

		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);

		$I->amOnRoute(static::ROUTE_CREATE_DEBT, ['issue_id' => $issue->getIssueId()]);
		$I->see('Create Debt Costs: ' . $issue->longId);
	}
}
