<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use common\fixtures\helpers\LeadFixtureHelper;
use common\helpers\Flash;
use common\models\user\Worker;
use common\modules\lead\controllers\DialerLeadController;
use common\modules\lead\models\LeadUser;

class DialerLeadCest {

	/** @see DialerLeadController::actionIndex() */
	public const ROUTE_INDEX = '/lead/dialer-lead/index';
	public const PERMISSION = Worker::PERMISSION_LEAD_DIALER;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		);
	}

	public function checkWithoutPermission(LeadManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuSubLink('Dialers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(LeadManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Dialers');
		$I->clickMenuSubLink('Dialers');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkWithoutLeadUserDialers(LeadManager $I): void {
		LeadUser::deleteAll([
			'type' => LeadUser::TYPE_DIALER,
		]);
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeFlash('Not Found Lead User as Dialer', Flash::TYPE_WARNING);
		$I->seeInCurrentUrl(LeadCest::ROUTE_INDEX);
	}

	public function checkLinkOnLeadIndex(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->dontSeeLink('Dialers');
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(LeadCest::ROUTE_INDEX);
		$I->seeLink('Dialers');
		$I->click('Dialers', '.lead-index');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}
}
