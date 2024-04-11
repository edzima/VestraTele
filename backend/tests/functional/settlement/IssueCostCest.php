<?php

namespace backend\tests\functional\settlement;

use backend\helpers\Url;
use backend\modules\settlement\controllers\CostController;
use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\CostIssueManager;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueCost;

class IssueCostCest {

	private IssueFixtureHelper $issueFixture;

	/* @see CostController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/cost/index';
	/* @see CostController::actionIssue() */
	public const ROUTE_ISSUE = '/settlement/cost/issue';
	/* @see CostController::actionCreate() */
	public const ROUTE_CREATE = '/settlement/cost/create';
	/* @see CostController::actionCreateInstallment() */
	public const ROUTE_CREATE_INSTALLMENT = '/settlement/cost/create-installment';
	/* @see CostController::actionView() */
	public const ROUTE_VIEW = '/settlement/cost/view';
	/* @see CostController::actionSettle() */
	public const ROUTE_SETTLE = '/settlement/cost/settle';
	/* @see CostController::actionSettlementLink() */
	public const ROUTE_SETTLEMENT_LINK = '/settlement/cost/settlement-link';
	/* @see CostController::actionSettlementUnlink() */
	public const ROUTE_SETTLEMENT_UNLINK = '/settlement/cost/settlement-unlink';

	public function _before(FunctionalTester $I): void {
		$this->issueFixture = new IssueFixtureHelper($I);
	}

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
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Costs');
	}

	public function checkMenuLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Costs');
		$I->clickMenuLink('Costs');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Costs');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Issue Type');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('Transfer Type');
		$I->seeInGridHeader('Date at');
		$I->seeInGridHeader('Deadline at');
		$I->seeInGridHeader('Settled at');
	}

	public function checkIssue(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::users(true),
				IssueFixtureHelper::types()
			)
		);

		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->see('Costs: ' . $issue->longId);
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Transfer Type');
		$I->seeInGridHeader('Value with VAT');
		$I->seeInGridHeader('VAT (%)');
		$I->seeInGridHeader('Date at');
		$I->seeInGridHeader('Settled at');
		$I->seeInGridHeader('Created at');
		$I->seeInGridHeader('Updated at');

		$I->seeLink('Create');
		$I->click('Create');
		$I->see('Create cost: ' . $issue->longId);
	}

	public function checkArchivedIssue(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(IssueFixtureHelper::issue());
		$issue = $this->issueFixture->grabIssue('archived');
		$I->amOnPage([static::ROUTE_ISSUE, 'id' => $issue->id]);
		$I->seeResponseCodeIs(404);
	}

	public function checkViewPage(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				SettlementFixtureHelper::cost(true),
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::users(),
			));
		codecept_debug(IssueCost::find()->andWhere(['id' => 1])->asArray()->one());
		$I->amOnPage([static::ROUTE_VIEW, 'id' => 1]);
		$I->see('Purchase of receivables');
		$I->see('615.00');
		$I->see('23,00%');
		$I->see('User');
	}

	public function checkCreate(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::users(),
				SettlementFixtureHelper::cost(false),
			)
		);
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_CREATE, 'id' => $issue->id]);
		$I->see('Create cost: ' . $issue->longId);
		$I->fillField('Value', 1230);
		$I->fillField('VAT (%)', 23);
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkCreateLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_INDEX]);
		$I->seeLink('Create Cost');
		$I->click('Create Cost');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function checkCreateInstallment(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::users(),
				SettlementFixtureHelper::cost(false),
			)
		);
		$issue = $this->issueFixture->grabIssue(0);
		$I->amOnPage([static::ROUTE_CREATE_INSTALLMENT, 'id' => $issue->id]);
		$I->seeElement('.field-issuecostform-type.hidden');
		$I->seeElement('.field-issuecostform-settled_at.hidden');
		$I->selectOption('User', $issue->agent->id);
		$I->fillField('Value', 123);
		$I->fillField('VAT (%)', 23);
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkSettlementLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
				SettlementFixtureHelper::settlement(),
				SettlementFixtureHelper::cost(true),
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::types(),
			)
		);

		$withoutSettlementsId = 4;
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $withoutSettlementsId]);
		$I->dontSee('Settlements', '#calculation-grid-container');
		$I->sendAjaxPostRequest(
			Url::toRoute([
				static::ROUTE_SETTLEMENT_LINK,
				'id' => $withoutSettlementsId,
				'settlementId' => 3,
			]), $I->getCSRF()
		);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $withoutSettlementsId]);
		$I->see('Settlements', '#calculation-grid-container');
	}

	public function checkSettlementUnLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::cost(true),
		));
		$withSettlementsId = 1;
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $withSettlementsId]);
		$I->see('Settlements', '#calculation-grid-container');
		$I->sendAjaxPostRequest(
			Url::toRoute([
				static::ROUTE_SETTLEMENT_UNLINK,
				'id' => $withSettlementsId,
				'settlementId' => 1,
			]), $I->getCSRF()
		);
		$I->amOnPage([static::ROUTE_VIEW, 'id' => $withSettlementsId]);
		$I->dontSee('Settlements', '#calculation-grid-container');
	}

	public function checkSettleAlreadySettledCost(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::cost(false),
		));
		$id = $I->haveRecord(IssueCost::class, [
			'issue_id' => 1,
			'date_at' => '2020-01-01',
			'settled_at' => '2020-01-01',
			'type' => IssueCost::TYPE_INSTALLMENT,
			'value' => 300,
			'vat' => 0,
		]);
		$I->amOnPage([static::ROUTE_INDEX]);
		$I->dontSeeElement('a', ['title' => 'Settle', 'href' => Url::toRoute([static::ROUTE_SETTLE, 'id' => $id])]);

		$I->amOnPage([static::ROUTE_SETTLE, 'id' => $id]);
		$I->seeFlash('Warning! Try settle already settled Cost.', 'warning');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkSettle(CostIssueManager $I): void {
		$I->amLoggedIn();
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			SettlementFixtureHelper::cost(false),
		));
		$id = $I->haveRecord(IssueCost::class, [
			'issue_id' => 1,
			'type' => IssueCost::TYPE_OFFICE,
			'value' => 300,
			'date_at' => '2020-01-01',
			'transfer_type' => IssueCost::TRANSFER_TYPE_BANK,
		]);
		$I->amOnPage([static::ROUTE_INDEX]);
		$I->seeElement('a', [
			'title' => 'Settle',
			'href' => Url::toRoute([
				static::ROUTE_SETTLE,
				'id' => $id,
				'redirectUrl' => Url::toRoute(static::ROUTE_INDEX),
			]),
		]);
		$I->amOnPage([static::ROUTE_SETTLE, 'id' => $id]);
		$I->fillField('Settled at', '2020-01-01');
		$I->click('Save');
		$I->seeRecord(IssueCost::class, [
			'id' => $id,
			'type' => IssueCost::TYPE_OFFICE,
			'settled_at' => '2020-01-01',
		]);
		$I->seeInCurrentUrl(static::ROUTE_INDEX);

		$I->dontSeeElement('a', [
			'title' => 'Settle',
			'href' => Url::toRoute([
				static::ROUTE_SETTLE,
				'id' => $id,
				'redirectUrl' => Url::toRoute(static::ROUTE_INDEX),
			]),
		]);
	}
}
