<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CostIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;

class CostExportCest {

	private const ROUTE_PCC = '/settlement/cost/pcc-export';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::customer(),
			SettlementFixtureHelper::cost(false),
		);
	}

	public function checkPCC(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_PCC);
		//@tood add file module
	}
}
