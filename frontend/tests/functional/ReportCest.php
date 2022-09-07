<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\Worker;
use frontend\controllers\ReportController;
use frontend\tests\_support\AgentTester;
use frontend\tests\FunctionalTester;
use Yii;

class ReportCest {

	/** @see ReportController::actionIndex() */
	private const ROUTE_INDEX = '/report/index';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			ProvisionFixtureHelper::provision(),
			ProvisionFixtureHelper::type(),
		);
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
	}

	public function checkSelfReportWithoutChildrenVisiblePermission(AgentTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->dontSee('Agents');
	}

	public function checkSelfReportWithChildrenVisiblePermission(AgentTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->assignPermission(Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeElement('#childes-select');
	}

	public function checkSelfReport(AgentTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see(
			strtr('Provisions Report ({from} - {to})', [
				'{from}' => Yii::$app->formatter->asDate('first day of this month'),
				'{to}' => Yii::$app->formatter->asDate('last day of this month'),
			])
		);
	}

	public function checkSelfChildrenWithoutPermission(AgentTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->amOnPage([static::ROUTE_INDEX, 'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER]);
		$I->seeResponseCodeIs(405);
	}

	public function checkSelfChildrenWithPermission(AgentTester $I): void {
		$I->amLoggedInAs(UserFixtureHelper::AGENT_PETER_NOWAK);
		$I->assignPermission(Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE);
		$I->amOnPage([static::ROUTE_INDEX, 'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER]);
		$I->see('Report: agnes-miller');
	}
}
