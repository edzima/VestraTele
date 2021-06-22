<?php

namespace frontend\tests\functional\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\user\User;
use common\modules\lead\controllers\SourceController;
use common\modules\lead\models\LeadSource;
use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class SourceCest {

	private const DEFAULT_TYPE_ID = 1;

	/* @see SourceController::actionIndex() */
	private const ROUTE_INDEX = '/lead/source/index';
	/* @see SourceController::actionCreate() */
	private const ROUTE_CREATE = '/lead/source/create';
	/* @see SourceController::actionView() */
	private const ROUTE_VIEW = '/lead/source/view';
	/* @see SourceController::actionDelete() */
	private const ROUTE_DELETE = '/lead/source/delete';

	private const SELECTOR_FORM = '#lead-source-form';
	private const SELECTOR_GRID = '#lead-source-grid';

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

	public function checkWithPermission(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Lead Sources', 'h1');
		$I->seeInGridHeader('ID');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('URL');
		$I->seeInGridHeader('Phone');
		$I->dontSeeInGridHeader('Owner');
		$I->seeInGridHeader('Sort Index');
	}

	public function checkCreateForm(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->dontSee('Owner', 'label');
	}

	public function checkCreateWithoutOwner(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('Test new name'));
		$I->seeRecord(LeadSource::class, [
			'owner_id' => 1,
			'name' => 'Test new name',
		]);
	}

	public function tryCreateWithOwnerAsOtherUser(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('Test new name', 2));
		$I->dontSeeRecord(LeadSource::class, [
			'owner_id' => 2,
			'name' => 'Test new name',
		]);
	}

	public function checkViewSelfModel(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$I->assignPermission(User::PERMISSION_LEAD);
		$id = $I->haveRecord(LeadSource::class, [
			'name' => 'Self source',
			'owner_id' => 1,
			'type_id' => static::DEFAULT_TYPE_ID,
		]);

		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $id]);
		$I->see('Self source', 'h1');
	}

	public function checkDeleteLead(FunctionalTester $I): void {
		$I->amLoggedInAs(1);
		$id = $I->haveRecord(LeadSource::class, [
			'name' => 'Self source',
			'owner_id' => 1,
			'type_id' => 1,
		]);
		$I->assignPermission(User::PERMISSION_LEAD);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeGridDeleteLink(static::SELECTOR_GRID);

		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => $id]), $I->getCSRF());
		$I->seeRecord(LeadSource::class, [
			'name' => 'Self source',
			'owner_id' => 1,
			'id' => $id,
		]);
		$I->seeResponseCodeIs(405);
	}

	protected function formParams($name, $owner_id = null, $type = self::DEFAULT_TYPE_ID, $sort_index = null): array {
		$params = [
			'LeadSourceForm[name]' => $name,
			'LeadSourceForm[sort_index]' => $sort_index,
			'LeadSourceForm[type]' => $type,
		];
		if ($owner_id !== null) {
			$params['LeadSourceForm[owner_id]'] = $owner_id;
		}
		return $params;
	}
}
