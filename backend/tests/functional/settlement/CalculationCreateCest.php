<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;

class CalculationCreateCest {

	/** @see CalculationController::actionCreate() */
	public const ROUTE = '/settlement/calculation/create';

	public function _before(CreateCalculationIssueManager $I): void {

		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::stageAndTypesFixtures(),
			IssueFixtureHelper::entityResponsible(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::type(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_SETTLEMENT_CREATE),
		));
		$I->amLoggedIn();
		$I->assignPermission(SettlementFixtureHelper::getTypeManagerPermission());
	}

	public function checkCreatePageWithCostPermission(CreateCalculationIssueManager $I): void {
		$I->assignPermission(User::PERMISSION_COST);
		$I->amOnCreatePage();
		$I->seeLink('Create cost');
	}

	public function checkCreatePage(CreateCalculationIssueManager $I): void {
		/** @var Issue $issue */
		$issue = $I->grabFixture(IssueFixtureHelper::ISSUE, 0);

		$I->amOnCreatePage($issue->id);
		$I->see('Create calculation for: ' . $issue->longId);
		$I->see($issue->customer->getFullName());
		$I->see($issue->type->name);
		$I->see($issue->stage->name);
	}

	public function checkSubmitEmpty(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage();
		$I->click('Save');
		$I->seeValidationError('Value with VAT cannot be blank.');
	}

	public function checkValid(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage(3, SettlementFixtureHelper::TYPE_ID_HONORARIUM);
		$I->dontSee('Problem status');
		$I->fillField('Value with VAT', 123);
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->seeLink('Update');
		$model = $I->grabRecord(IssuePayCalculation::class, [
			'issue_id' => 3,
			'value' => 123,
			'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $model->id,
			'value' => 123,
		]);
		$I->seeEmailIsSent(2);
		$I->dontSeeJobIsPushed(); //sms to customer default disable
	}

	public function checkCreateWithoutSendEmailToWorker(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage(3);
		$I->fillField('Value with VAT', 123);
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendemailtoworkers');
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->seeEmailIsSent(1);
	}

	public function checkCreateWithoutSendEmailToCustomer(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage(3);
		$I->fillField('Value with VAT', 123);
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendemailtocustomer');
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->seeEmailIsSent(1);
	}

	public function checkCreateWithoutSendEmails(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage(1);
		$I->fillField('Value with VAT', 123);
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendemailtocustomer');
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendemailtoworkers');
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->dontSeeEmailIsSent();
	}

	public function checkCreateWithoutSendSmsToCustomer(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage(3);
		$I->fillField('Value with VAT', 123);
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendsmstocustomer');
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->seeJobIsPushed(0);
	}

	public function checkCreateWithoutSendSms(CreateCalculationIssueManager $I): void {
		$I->amOnCreatePage();
		$I->fillField('Value with VAT', 123);
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendsmstocustomer');
		$I->uncheckOption('#issuesettlementcreatemessagesform-sendsmstoagent');
		$I->selectOption('Provider', IssuePayCalculation::PROVIDER_CLIENT);
		$I->click('Save');
		$I->dontSeeJobIsPushed();
	}

}
