<?php

namespace backend\tests\acceptance\issue;

use backend\tests\Page\issue\IssueFormPage;
use backend\tests\Step\acceptance\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\user\Customer;

class IssueFormCest {

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function _before(IssueManager $I): void {
		$I->amLoggedIn();
	}

	public function checkCreate(IssueManager $I, IssueFormPage $page): void {
		/** @var Customer $customer */
		$customer = $I->grabFixture(IssueFixtureHelper::CUSTOMER, 0);
		$page->amOnCreatePage($customer->getId());
		$I->seeInTitle('Create issue for: ' . $customer->getFullName());
		$page->selectType('Accident');
		$page->selectStage('Completing documents');
		$page->selectEntityResponsible('Alianz');
		$page->selectLawyer('lawyer1');
		$page->selectAgent('agent1');
		$page->fillSigningAt('2020-02-02');
		$page->clickSubmitButton();
		$I->wait(1);
		$I->seeLink('Update');
		$I->see($customer->getFullName());
		$I->see('Accident');
		$I->see('Completing documents');
		$I->see('Alianz');
		$I->see('Agent - agent1');
		$I->see('Lawyer - lawyer1');
		$I->see('Client - Wayne John');
	}

	public function checkTypeChange(IssueManager $I, IssueFormPage $page): void {
		$page->amOnCreatePage();
		$page->selectType('Accident');
		$I->see('Completing documents');
		$I->wantTo('Check Type without Completing documents stage');
		$page->selectType('Benefits - civil proceedings');
		$I->dontSee('Completing documents');
	}

	public function checkStageChangeOnCreatePage(IssueManager $I, IssueFormPage $page): void {
		$page->amOnCreatePage();
		$page->selectType('Accident');
		$page->selectStage('Completing documents');
		$I->dontSee(IssueFormPage::FIELD_STAGE_CHANGE_AT);
		$page->selectStage('Proposal');
		$I->dontSee(IssueFormPage::FIELD_STAGE_CHANGE_AT);
	}

	public function checkStageChangeOnUpdatePage(IssueManager $I, IssueFormPage $page): void {
		$page->amOnUpdatePage(1);
		$I->see(IssueFormPage::FIELD_STAGE_CHANGE_AT);
		$page->fillStageChangeAt('2020-02-02');
		$I->dontSee('Proposal');
	}

	public function checkSendEmpty(IssueManager $I, IssueFormPage $page): void {
		$page->amOnCreatePage();
		$page->clickSubmitButton();
		$I->wait(0.3);
		$I->seeValidationError('Type cannot be blank.');
		$I->seeValidationError('Signing at cannot be blank.');
		$I->seeValidationError('Entity responsible cannot be blank.');
		$I->seeValidationError('lawyer cannot be blank.');
		$I->seeValidationError('agent cannot be blank.');
	}

}
