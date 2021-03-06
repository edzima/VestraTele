<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationMinCountController;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\StageType;

class CalculationMinCountCest {

	/** @see CalculationMinCountController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/calculation-min-count/index';

	/** @see CalculationMinCountController::actionSet() */
	public const ROUTE_SET = '/settlement/calculation-min-count/set';

	/** @see CalculationMinCountController::actionUpdate() */
	public const ROUTE_UPDATE = '/settlement/calculation-min-count/update';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Min calculation count');
		$I->amOnPage(static::ROUTE_SET);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLink(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Min calculation count');
		$I->clickMenuLink('Min calculation count');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndex(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->see('Calculations min counts');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Stage');
		$I->seeInGridHeader('Min calculation count');
	}

	public function checkValidCreate(CreateCalculationIssueManager $I): void {
		$I->haveFixtures(IssueFixtureHelper::stageAndTypesFixtures());
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_SET);
		$I->submitForm('#calculation-min-count-form', [
			'CalculationMinCountForm' => [
				'stageId' => 1,
				'typeId' => 1,
				'minCount' => 2,
			],
		]);

		$I->seeRecord(StageType::class, [
			'stage_id' => 1,
			'type_id' => 1,
			'min_calculation_count' => 2,
		]);
	}

}
