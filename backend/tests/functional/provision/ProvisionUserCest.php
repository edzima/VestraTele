<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\UserController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\provision\ProvisionUser;

class ProvisionUserCest {

	/** @see UserController::actionIndex() */
	public const ROUTE_INDEX = '/provision/user/index';
	/** @see UserController::actionCreate() */
	public const ROUTE_CREATE = '/provision/user/create';
	/** @see UserController::actionUserView() */
	public const ROUTE_USER_VIEW = '/provision/user/user-view';

	private const FORM_SELECTOR = '#provision-user-form';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::type(),
		);
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Schemas');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProvisionManager(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Schemas');
		$I->clickMenuLink('Schemas');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInTitle('Schemas', 'h1');
		$I->seeCheckboxIsChecked('Only self');
		$I->seeInGridHeader('User');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Value');
		$I->seeInGridHeader('From at');
		$I->seeInGridHeader('To at');
		$I->seeInGridHeader('Overwritten');
	}

	public function checkCreateLink(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeLink('Create provision schema');
		$I->click('Create provision schema');
		$I->seeInCurrentUrl(static::ROUTE_CREATE);
	}

	public function checkCreateSubmitEmpty(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, []);
		$I->seeValidationError('Value cannot be blank.');
	}

	public function checkCreateOnlyValue(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->fillField('Value', 55);
		$I->submitForm(static::FORM_SELECTOR, []);
		$I->seeRecord(ProvisionUser::class, [
			'value' => 55,
		]);
	}

}
