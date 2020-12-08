<?php

namespace backend\tests\functional\settlement;

use backend\tests\Step\Functional\CalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use common\models\user\User;

class CalculationWithoutProvisionsCest {

	public const ROUTE = '/settlement/calculation/without-provisions';

	public function checkPageAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIs(403);
	}

	public function checkPageAsCalculationManager(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Settlements without provisions');
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLinkWithProvisionPermission(CalculationIssueManager $I):void{
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->seeMenuLink('Settlements without provisions');
		$I->clickMenuLink('Settlements without provisions');
		$I->seeInCurrentUrl(static::ROUTE);
	}

	public function checkPageAsCalculationManagerWithProvisionPermission(CalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE);
		$I->seeResponseCodeIsSuccessful();
		$I->see('Settlements without provisions', 'h1');
		$I->dontSeeLink('Create');
		$I->seeInGridHeader('Issue');
		$I->seeInGridHeader('Type');
	}

	public function checkIndexPageLinkAsCalculationManager(CalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Settlements without provisions');
		$I->amOnRoute(CalculationCest::ROUTE_INDEX);
		$I->dontSeeLink('Without provisions');
	}

	public function checkIndexPageLinkAsCalculationManagerWithProvisionPermission(CalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_PROVISION);
		$I->amLoggedIn();
		$I->amOnRoute(CalculationCest::ROUTE_INDEX);
		$I->seeLink('Without provisions');
	}

}
