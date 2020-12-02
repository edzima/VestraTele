<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssuePay;
use frontend\tests\_support\AgentTester;
use frontend\tests\_support\ProviderPayTester;

class CalculationCest {

	public const ROUTE_INDEX = '/calculation/index';
	public const ROUTE_ISSUE = '/calculation/issue';

	public function checkAsAgent(AgentTester $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Calculations issues');
		$I->dontSeeLink('Calculations to provider');
	}

	public function checkTable(AgentTester $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Value');
	}

	public function checkOwnerCalculation(AgentTester $I): void {
		$I->haveFixtures(array_merge(IssueFixtureHelper::fixtures(), IssueFixtureHelper::settlements()));
		$I->amOnPage(static::ROUTE_VIEW);
	}

	public function checkOwnerCalculationWithoutArchivesPermission(): void {
		//$I->see
		//add warning log
		$I->seeResponseCodeIs(403);
	}

	public function checkOwnerCalculationWithArchivesPermission(): void {

	}

	public function checkNotOwnerCalculation(AgentTester $I): void {
		$I->seeResponseCodeIs(403);
	}

	public function checkAddNoteAsOwner(AgentTester $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnPage(static::ROUTE_INDEX);
	}

	public function checkAddNoteInDontOwnerCalculation(AgentTester $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnPage(static::ROUTE_INDEX);
	}

	public function checkEditPayAsAgent(AgentTester $I): void {
		$I->seeResponseCodeIs(403);
	}

	public function checkEditPayAsPayAgent(PayAgentTester $I): void {
		$I->seeResponseCodeIs(400);
	}

	public function checkEditDeadlineAtInCalculationPay(PayAgentTester $I): void {
		$I->seeLink('Edit pay');
	}

	public function checkIndexPageAsProvider(AgentTester $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Calculations');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Calculations issues');
		$I->dontSeeLink('Calculations to provider');
	}

	public function checkViewPageAsAgent(AgentTester $I): void {
		$I->dontSeeLink('Edit pay');
		$I->see('Type');
		$I->see('Issue type');
		$I->see('Issue stage');
		$I->seeInGridHeader('Value');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Status problem');
		$I->seeInGridHeader('Owner');
		// all agent can create note.
		$I->seeLink('Create note');
		$I->see('Provisions');
		$I->see('All provision');
		$I->see('Paymemnt provisions');
		$I->see('Dont payment provision');
		$I->see('Pays');

		$I->dontSee('Edit deadline at');
		$I->dontSee('Mark as payment');
	}

	public function checkViewPageAsPayAgent(PayAgentTester $I): void {
		$I->see('Pays');
		$I->see('Edit deadline at');
		$I->dontSee('Mark as payment');
	}

	public function checkViewPageAsProviderPay(ProviderPayTester $I): void {
		$I->see('Pays');
		$I->see('Mark as payment');
		$I->see('Edit deadline at');
	}

	public function checkEditDeadlineAsAgent(AgentTester $I): void {
		$I->seeResponseCodeIs(403);
	}

	public function checkEditDeadlineAsPayAgentInOwnerIssue(AgentTester $I): void {
		$I->seeResponseCodeIs(400);
		$I->fillField('Deadline_at');
		$I->click('Save');
		$I->seeRecord(IssuePay::class, [
			'deadline_at' => '',
		]);
	}

	public function checkEditDeadlineAsPayAgentInNotOwnerIssue(AgentTester $I): void {
		$I->seeResponseCodeIs(403);
	}

	public function checkMarkAsPaymentAsAgent(AgentTester $I): void {
		$I->seeResponseCodeIs(403);
	}

	public function checkMarkAsPaymentAsProviderPay(ProviderPayTester $I): void {
		$I->see('Mark as payment pay: {payNumber}');
		$I->fillField('Payment at');
		$I->click('Save');
		$I->seeRecord(IssuePay::class, [
			'payment_at' => '',
		]);
		$I->see(ProviderPayModel::class, [
			'user_id' => '',
			'pay_id' =>'',
			'provider_at' => null,
		]);
	}

	public function checkProviderIndexPage(ProviderPayTester $I): void {
		$I->amLoggedIn();
		$I->seeLink('Calculations to provider');
		$I->click('Calculations to provider');
		$I->see('Customer lastname form');
		$I->fillField('Lastname', 'War');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('View');
		$I->dontSeeLink('Delete');
		$I->click('View');
	}

}
