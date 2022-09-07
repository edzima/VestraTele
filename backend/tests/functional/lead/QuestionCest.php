<?php

namespace backend\tests\functional\lead;

use backend\tests\Step\Functional\LeadManager;
use backend\tests\Step\Functional\Manager;
use common\modules\lead\controllers\QuestionController;
use common\modules\lead\models\LeadQuestion;

class QuestionCest {

	/* @see QuestionController::actionIndex() */
	public const ROUTE_INDEX = '/lead/question/index';
	/* @see QuestionController::actionCreate() */
	private const ROUTE_CREATE = '/lead/question/create';

	private const FORM_SELECTOR = '#lead-question-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Questions');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsLeadManager(LeadManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Questions');
		$I->clickMenuLink('Questions');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkIndexPage(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInTitle('Lead Questions');

		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Placeholder');
		$I->seeInGridHeader('Status');
		$I->seeInGridHeader('Type');
		$I->seeInGridHeader('Is required');
		$I->seeInGridHeader('Show in grid');
	}

	public function checkCreateOnlyName(LeadManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->see('Create Lead Question');
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('New lead question', null, null));
		$I->seeRecord(LeadQuestion::class, [
			'name' => 'New lead question',
		]);
	}

	private function formParams($name, $placeholder, $is_required) {
		return [
			'LeadQuestionForm[name]' => $name,
			'LeadQuestionForm[placeholder]' => $placeholder,
			'LeadQuestionForm[is_required]' => $is_required,
		];
	}

}
