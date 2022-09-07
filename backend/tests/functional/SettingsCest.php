<?php

namespace backend\tests\functional;

use backend\controllers\SiteController;
use backend\tests\Step\Functional\Admin;
use backend\tests\Step\Functional\Manager;
use common\models\user\User;

class SettingsCest {

	/**
	 * @see SiteController::actionSettings()
	 */
	public const ROUTE = 'site/settings';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Application settings');
		$I->see('Registration');
		$I->see('Email confirm');
		$I->see('Backend Theme');
		$I->see('Fixed backend layout');
		$I->see('Boxed backend layout');
		$I->see('Backend sidebar collapsed');
		$I->see('Backend sidebar mini');
		$I->dontSee('Robot SMS Owner');
		$I->dontSee('Settlement types for provisions');
	}

	public function checkAsAdmin(Admin $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->see('Application settings');
		$I->see('Registration');
		$I->see('Email confirm');
		$I->see('Backend Theme');
		$I->see('Fixed backend layout');
		$I->see('Boxed backend layout');
		$I->see('Backend sidebar collapsed');
		$I->see('Backend sidebar mini');
		$I->see('Robot SMS Owner');
	}

	public function checkWithProvisionsPermission(Admin $I): void {
		$I->amLoggedIn();
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amOnRoute(static::ROUTE);
		$I->see('Settlement types for provisions');
	}

}
