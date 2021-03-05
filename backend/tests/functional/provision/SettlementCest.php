<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\SettlementController;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\helpers\Flash;
use common\models\issue\IssuePayCalculation;
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
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::issueType(),
		);
	}

	public function checkViewPage(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('not-payed');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $settlement->id]);
		$I->see('Settlement provisions: ' . $settlement->getTypeName());
	}

	public function checkTypesLinkOnViewPage(ProvisionManager $I): void {
		$settlement = $this->grabSettlement('not-payed');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $settlement->id]);
		$I->seeLink('Provisions types');
		$I->click('Provisions types');
		$I->seeInCurrentUrl(ProvisionTypeCest::ROUTE_SETTLEMENT);
		$I->see('Provisions types for settlement: ' . $settlement->getTypeName());
	}

	public function checkSettlementWithoutTypes(ProvisionManager $I): void {
		$this->goToUserPage($this->grabSettlement('lawyer')->id, IssueUser::TYPE_AGENT);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Generate provisions for: agent - agent2');
		$I->seeFlash('Not active types for this settlement.', Flash::TYPE_WARNING);
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

	private function grabSettlement(string $index): IssuePayCalculation {
		return $this->tester->grabFixture(IssueFixtureHelper::CALCULATION, $index);
	}
}
