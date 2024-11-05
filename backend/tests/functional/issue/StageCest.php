<?php

namespace backend\tests\functional\issue;

use backend\modules\issue\controllers\StageController;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\user\Worker;

class StageCest {

	/** @see StageController::actionIndex() */
	private const ROUTE_INDEX = '/issue/stage/index';
	/** @see StageController::actionCreate() */
	private const ROUTE_CREATE = '/issue/stage/create';

	private const FORM_SELECTOR = '#issue-stage-form';

	private const PERMISSION = Worker::PERMISSION_ISSUE_STAGE_MANAGER;

	public function _fixtures(): array {
		return IssueFixtureHelper::stageAndTypesFixtures();
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Stages');
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManager(IssueManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->dontSeeMenuSubLink('Stages');
		$I->seeResponseCodeIs(403);
	}

	public function checkAsIssueManagerWithStagePermission(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->seeMenuSubLink('Stages');
		$I->clickMenuSubLink('Stages');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(static::PERMISSION);
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Shortname');
		$I->seeInGridHeader('Issues Types');
		$I->seeInGridHeader('Reminder (days)');
		$I->seeInGridHeader('Order');
		$I->seeInGridHeader('Issues Count');
		$I->seeInGridHeader('Calendar Background');
	}

	public function checkCreate(IssueManager $I): void {
		$I->assignPermission(static::PERMISSION);
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(
			'Some new stage name',
			'SNTM',
			[1, 2],
		)
		);
		$I->seeInTitle('Some new stage name');
	}

	private function formParams($name, $shortname, $typesIds): array {
		return [
			'IssueStageForm[name]' => $name,
			'IssueStageForm[short_name]' => $shortname,
			'IssueStageForm[typesIds]' => $typesIds,
		];
	}

}
