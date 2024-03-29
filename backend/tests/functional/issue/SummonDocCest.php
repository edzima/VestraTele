<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\SummonDocController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\models\issue\SummonDoc;

class SummonDocCest {

	/** @see SummonDocController::actionIndex() * */
	public const ROUTE_INDEX = '/issue/summon-doc/index';

	/** @see SummonDocController::actionCreate() * */
	public const ROUTE_CREATE = '/issue/summon-doc/create';

	public function checkIndexPageNotAllowedAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
		$I->amOnPage(SummonCest::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Docs');
	}

	public function checkIndexPageAsSummonManager(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->amOnPage(SummonCest::ROUTE_INDEX);
		$I->seeMenuSubLink('Summon Docs Types');
		$I->clickMenuSubLink('Summon Docs Types');
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Priority');
		$I->seeInGridHeader('Summon Types');
	}

	public function checkCreate(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->see('Create Summon Doc');
		$I->fillField('Name', 'Test Summon Doc');
		$I->click('Save');
		$I->seeRecord(SummonDoc::class, [
			'name' => 'Test Summon Doc',
		]);
	}
}
