<?php

namespace backend\tests\functional\lead;

use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\controllers\LeadController;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadStatusInterface;

class LeadCest {

	/** @see LeadController::actionIndex() */
	public const ROUTE_INDEX = '/lead/lead/index';
	/** @see LeadController::actionCreate() */
	private const ROUTE_CREATE = '/lead/lead/create';
	/** @see LeadController::actionView() */
	private const ROUTE_VIEW = '/lead/lead/view';

	private const SELECTOR_SEARCH_LABEL = '.lead-search label';
	private const SELECTOR_FORM = '#lead-form';

	private FunctionalTester $tester;

	public function _before(FunctionalTester $I): void {
		$this->tester = $I;
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Leads');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Leads');
		$I->clickMenuLink('Leads');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->see('Leads', 'h1');
	}

	public function checkIndexSearchFields(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Email', static::SELECTOR_SEARCH_LABEL);
		$I->see('Provider', static::SELECTOR_SEARCH_LABEL);
		$I->see('User', static::SELECTOR_SEARCH_LABEL);
		$I->see('Without User', static::SELECTOR_SEARCH_LABEL);
		$I->see('Without Report', static::SELECTOR_SEARCH_LABEL);

		$I->see('Closed Questions', static::SELECTOR_SEARCH_LABEL);
		$I->see('Region', 'label');
		$I->see('Code', 'label');
		$I->see('City', 'label');
	}

	public function checkIndexGrid(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Reports');
	}

	public function checkCreateLink(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Create Lead');
		$I->click('Create Lead');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
		$I->see('Create Lead', 'h1');
	}

	public function checkCreateEmpty(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, []);
		$I->seeValidationError('Name cannot be blank.');
		$I->seeValidationError('Phone cannot be blank when email is blank.');
		$I->seeValidationError('Email cannot be blank when phone is blank.');
	}

	public function checkCreateWithPhone(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, [
			'LeadForm[source_id]' => 1,
			'LeadForm[status_id]' => LeadStatusInterface::STATUS_NEW,
			'LeadForm[name]' => 'Jonny',
			'LeadForm[phone]' => '555-222-111',
		]);
		$I->seeFlash(' Success create Lead. ', 'success');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkViewPage(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$this->goToViewPage();
		$I->seeLink('Report');
		$I->seeLink('Create Reminder');
		$I->seeLink('Update');
		$I->seeLink('Delete');
	}

	public function checkReportLink(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$this->goToViewPage();
		$I->seeLink('Report');
		$I->click('Report');
		$I->seeInCurrentUrl(LeadReportCest::ROUTE_REPORT);
	}

	public function checkReminderLink(LeadManager $I): void {
		$I->haveFixtures(LeadFixtureHelper::leads());
		$I->amLoggedIn();
		$this->goToViewPage();
		$I->seeLink('Create Reminder');
		$I->click('Create Reminder');
		$I->seeInCurrentUrl(ReminderCest::ROUTE_REPORT);
	}

	private function goToViewPage(int $id = null): void {
		if ($id === null) {
			$id = $this->grabLead()->getId();
		}
		$this->tester->amOnRoute(static::ROUTE_VIEW, ['id' => $id]);
	}

	private function grabLead(string $index = 'new-wordpress-accident'): ActiveLead {
		return $this->tester->grabFixture(LeadFixtureHelper::LEAD, $index);
	}

}
