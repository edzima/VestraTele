<?php

namespace backend\tests\acceptance\issue;

use backend\helpers\Url;
use backend\tests\AcceptanceTester;
use backend\tests\Step\acceptance\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\Customer;

class IssueCreateCest {

	protected const ROUTE_CREATE = '/issue/issue/create';

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

	public function checkCreate(AcceptanceTester $I): void {
		/** @var Customer $customer */
		$customer = $I->grabFixture(IssueFixtureHelper::CUSTOMER, 0);
		$I->amOnPage(Url::to(['/issue/issue/create', 'customerId' => $customer->id]));
		$I->seeInTitle('Create issue for: ' . $customer->getFullName());
		$I->fillOutSelect2OptionField('.field-issueform-type_id', 'Accident');
		$I->wait(2);
		$I->fillOutSelect2OptionField('.field-issueform-stage_id', 'Completing documents');
		$I->fillOutSelect2OptionField('.field-issueform-entity_responsible_id', 'Alianz');

		$I->fillOutSelect2OptionField('.field-issueform-lawyer_id', 'lawyer1');
		$I->fillOutSelect2OptionField('.field-issueform-agent_id', 'agent1');
		$I->fillField('Signing at', '2020-02-02');
		$I->click('#issue-form button[type=submit]');
		$I->wait(2);
		$I->seeLink('Update');
		$I->see($customer->getFullName());
		$I->see('Accident');
		$I->see('Completing documents');
		$I->see('Alianz');
		$I->see('Agent - agent1');
		$I->see('Lawyer - lawyer1');
		$I->see('Client - Wayne John');
	}

	public function checkSendEmpty(AcceptanceTester $I): void {
		$I->amOnPage(Url::to(['/issue/issue/create', 'customerId' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID]));
		$I->click('#issue-form button[type=submit]');
		$I->wait(1);
		$I->seeValidationError('Type cannot be blank.');
		$I->seeValidationError('Signing at cannot be blank.');
		$I->seeValidationError('Entity responsible cannot be blank.');
		$I->seeValidationError('lawyer cannot be blank.');
		$I->seeValidationError('agent cannot be blank.');
	}

}
