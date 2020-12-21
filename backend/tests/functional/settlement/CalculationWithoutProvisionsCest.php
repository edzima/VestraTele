<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\Bookkeeper;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\user\User;

class CalculationWithoutProvisionsCest {

	/** @see CalculationController::actionWithoutProvisions() */
	public const ROUTE = '/settlement/calculation/without-provisions';
	private const MENU_LINK_TEXT = 'Without provisions';

	public function checkPageAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIs(403);
	}

	public function checkPageAsCalculationManager(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink(static::MENU_LINK_TEXT);
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLinkWithProvisionPermission(CreateCalculationIssueManager $I):void{
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->seeMenuLink(static::MENU_LINK_TEXT);
		$I->clickMenuLink(static::MENU_LINK_TEXT);
		$I->seeInCurrentUrl(static::ROUTE);
	}

	public function checkPageAsCalculationManagerWithProvisionPermission(Bookkeeper $I): void {
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Settlements without provisions', 'h1');
		$I->dontSeeLink('Create');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
	}

	public function checkIndexPageLinkAsCalculationManager(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink(static::MENU_LINK_TEXT);
		$I->amOnRoute(CalculationCest::ROUTE_INDEX);
		$I->dontSeeLink('Without provisions');
	}

	public function checkIndexPageLinkAsCalculationManagerWithProvisionPermission(CreateCalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->amOnRoute(CalculationCest::ROUTE_INDEX);
		$I->seeLink('Without provisions');
	}

}
