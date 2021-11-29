<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\LeadController;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use common\tests\helpers\LeadFactory;
use frontend\tests\_support\LeadTester;
use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class LeadCest {

	/* @see LeadController::actionIndex() */
	private const ROUTE_INDEX = '/lead/lead/index';
	/* @see LeadController::actionCreate() */
	private const ROUTE_CREATE = '/lead/lead/create';
	/* @see LeadController::actionCreateFromSource() */
	private const ROUTE_CREATE_FROM_SOURCE = '/lead/lead/create-from-source';
	/* @see LeadController::actionView() */
	public const ROUTE_VIEW = '/lead/lead/view';
	/* @see LeadController::actionDelete() */
	private const ROUTE_DELETE = '/lead/lead/delete';
	/* @see LeadController::actionPhone() */
	private const ROUTE_PHONE = '/lead/lead/phone';

	private const SELECTOR_LEAD_GRID = '#leads-grid';

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkAsGuest(FunctionalTester $I): void {
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInLoginUrl();
	}

	public function checkWithoutPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeInGridHeader('Owner');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Source');
		$I->seeInGridHeader('Phone');
		$I->seeInGridHeader('Reports');
	}

	public function checkPhonePage(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_PHONE);
		$I->fillField('Phone', '777-222-122');
		$I->click('Search');
		$I->see('Leads');
	}

	public function checkAsUserWithoutLeads(FunctionalTester $I): void {
		$I->amLoggedInAs(3);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('No results found.', static::SELECTOR_LEAD_GRID);
	}

	public function checkAsUserWithLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-1 of 1 item.', static::SELECTOR_LEAD_GRID);
		Module::manager()->pushLead(LeadFactory::createLead([
			'owner_id' => 1,
			'phone' => '505-505-505',
			'name' => 'Jonny',
			'source_id' => 1,
			'status_id' => 1,
		]));
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Showing 1-2 of 2 items.', static::SELECTOR_LEAD_GRID);
	}

	public function trySearchNotSelfLeads(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX, [
			'LeadSearch[user_id]' => 2,
		]);
	}

	public function checkCreateEmpty(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->click('Save');
		$I->seeValidationError('Name cannot be blank.');
		$I->seeValidationError('Phone cannot be blank when email is blank.');
		$I->seeValidationError('Email cannot be blank when phone is blank.');
	}

	public function checkCreateOnlyPhone(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->fillField('Lead Name', 'Jonny Test');
		$I->fillField('Phone', '789-789-789');
		$I->click('Save');
		$I->seeRecord(Lead::class, [
			'name' => 'Jonny Test',
		]);
		/** @var Lead $lead */
		$lead = $I->grabRecord(Lead::class, [
			'name' => 'Jonny Test',
		]);
		$I->seeRecord(LeadUser::class, [
			'type' => LeadUser::TYPE_OWNER,
			'lead_id' => $lead->getId(),
			'user_id' => $I->getUser()->getId(),
		]);
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
	}

	public function checkCreateFromSource(LeadTester $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE_FROM_SOURCE, ['id' => 1]);
		$I->see('Create Lead: wordpress-homepage');
		$I->fillField('Lead Name', 'Jonny Test');
		$I->fillField('Phone', '789-789-789');
		$I->fillField('Details', 'Jonny Details');
		$I->click('Save');
		$I->seeInCurrentUrl(static::ROUTE_VIEW);
		$I->seeRecord(Lead::class, [
			'name' => 'Jonny Test',
		]);
		/** @var Lead $lead */
		$lead = $I->grabRecord(Lead::class, [
			'name' => 'Jonny Test',
		]);
		$I->seeRecord(LeadUser::class, [
			'lead_id' => $lead->getId(),
			'type' => LeadUser::TYPE_OWNER,
			'user_id' => $I->getUser()->getId(),
		]);
		$I->seeRecord(LeadReport::class, [
			'owner_id' => $I->getUser()->getId(),
			'details' => 'Jonny Details',
			'lead_id' => $lead->getId(),
		]);
	}

	public function checkViewPageSelfLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 1]);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Status');
		$I->see('Type');
		$I->see('Source');
		$I->see('Campaign');
		$I->see('Phone');
		$I->see('Email');
		$I->see('Users');
	}

	public function checkViewPageShortReportLink(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 1]);
		$I->seeLink('Not Answered');
	}

	public function checkViewPageOnlySelfLead(FunctionalTester $I): void {
		$I->amLoggedInAs(2);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 2]);
		$I->seeResponseCodeIsSuccessful();
		$I->dontSee('Users');
	}

	public function checkViewPageNotSelfLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 2]);
		$I->seeResponseCodeIs(404);
	}

	public function checkDeleteLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeGridDeleteLink(static::SELECTOR_LEAD_GRID);
		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => 2]), $I->getCSRF());
		$I->seeResponseCodeIs(405);
	}

	public function checkSameContacts(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => 1]);
		$I->seeFlash('Find Similars Leads: 1.', 'warning');
		$I->see('Same Contacts Leads', 'h3');
		$I->dontSeeLink('John2');
		$I->see('Type', '.same-contact-lead');
		$I->see('Source', '.same-contact-lead');
		$I->see('Phone', '.same-contact-lead');
		$I->see('Owner', '.same-contact-lead');
	}

}
