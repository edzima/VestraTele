<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\ReportController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use Yii;

class ProvisionReportCest {

	/** @see ReportController::actionIndex() */
	public const ROUTE_INDEX = '/provision/report/index';

	/** @see ReportController::actionView() */
	public const ROUTE_VIEW = '/provision/report/view';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Reports');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProvisionManager(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Reports');
		$I->clickMenuLink('Reports');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Reports', 'h1');
		$I->seeInGridHeader('User');
	}

	public function checkViewPage(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::agent(true),
				SettlementFixtureHelper::pay(),
				ProvisionFixtureHelper::provision()
			)
		);

		$I->amOnPage([
			static::ROUTE_VIEW,
			'id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'dateFrom' => '2020-01-01',
			'dateTo' => '2021-01-01',
		]);

		$I->seeInTitle('Report: Nowak Peter ('
			. Yii::$app->formatter->asDate('2020-01-01')
			. ' - '
			. Yii::$app->formatter->asDate('2021-01-01')
		);

		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Customer');
	}
}
