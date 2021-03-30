<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use frontend\tests\_support\AgentTester;
use frontend\tests\FunctionalTester;

class ReportCest {

	public function _fixtures(): array {
		return array_merge(
			['agent' => UserFixtureHelper::agent()],
			ProvisionFixtureHelper::provision(),
			ProvisionFixtureHelper::type(),
		);
	}

	private const ROUTE_INDEX = '/report/index';

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
	}

	public function checkReportPage(AgentTester $I): void {

		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		//$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Reports');
	}
}
