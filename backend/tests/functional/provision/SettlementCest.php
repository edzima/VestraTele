<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\SettlementController;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\helpers\Flash;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;

class SettlementCest {

	/** @see SettlementController::actionView() */
	public const ROUTE_VIEW = '/provision/settlement/view';
	/** @see SettlementController::actionUser() */
	public const ROUTE_USER = '/provision/settlement/user';

	private ProvisionManager $tester;

	public function _before(ProvisionManager $I): void {
		$this->tester = $I;
		$this->tester->amLoggedIn();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::cost(true),
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::issueType(),
			ProvisionFixtureHelper::provision(),
		);
	}

	public function checkViewPageWithCosts(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('not-payed-with-double-costs');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $settlement->id]);
		$I->see('Settlement provisions: ' . $settlement->getTypeName());
		$I->see('Value without costs');
	}

	public function checkViewPageWithoutCosts(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('many-pays-without-costs');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $settlement->id]);
		$I->see('Settlement provisions: ' . $settlement->getTypeName());
		$I->dontSee('Value without costs');
	}

	public function checkTypesLinkOnViewPage(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('many-pays-without-costs');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $settlement->id]);
		$I->seeLink('Provisions types');
		$I->click('Provisions types');
		$I->seeInCurrentUrl(ProvisionTypeCest::ROUTE_SETTLEMENT);
		$I->see('Provisions types for settlement: ' . $settlement->getTypeName());
	}

	public function checkSettlementWithoutTypes(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('lawyer');
		codecept_debug($settlement->getId());

		codecept_debug($settlement);
		$this->goToUserPage($settlement->getId(), IssueUser::TYPE_AGENT);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Generate provisions for: agent - ' . $settlement->getIssueModel()->agent->getFullName());
		$I->seeFlash('Not active agent types for settlement: ' . $settlement->getTypeName(), Flash::TYPE_WARNING);
		$I->seeLink('Create provision type');
		$I->click('Create provision type');
		$I->seeInField('Name', 'Lawyer - Accident');
	}

	private function goToUserPage(int $settlementId, string $issueUserType, int $typeId = null): void {
		$this->tester->amOnRoute(static::ROUTE_USER, [
			'id' => $settlementId,
			'issueUserType' => $issueUserType,
			'typeId' => $typeId,
		]);
	}

	private function grabSettlement(string $index): IssueSettlement {
		return $this->tester->grabFixture(SettlementFixtureHelper::SETTLEMENT, $index);
	}
}
