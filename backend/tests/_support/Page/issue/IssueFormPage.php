<?php

namespace backend\tests\Page\issue;

use backend\tests\Step\acceptance\IssueManager;
use common\fixtures\helpers\UserFixtureHelper;
use yii\helpers\Url;

class IssueFormPage {

	/** @see IssueController::actionCreate() */
	public const ROUTE_CREATE = '/issue/issue/create';
	/** @see IssueController::actionUpdate() */
	public const ROUTE_UPDATE = '/issue/issue/update';

	protected const DEFAULT_CUSTOMER_ID = UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID;

	public const FIELD_SIGNING_AT = 'Signing at';
	public const FIELD_STAGE_CHANGE_AT = 'Stage date';

	protected IssueManager $tester;

	public function __construct(IssueManager $I) {
		$this->tester = $I;
	}

	public function amOnCreatePage(int $customerId = self::DEFAULT_CUSTOMER_ID): void {
		$this->tester->amOnPage(Url::to([static::ROUTE_CREATE, 'customerId' => $customerId]));
	}

	public function amOnUpdatePage(int $issueId): void {
		$this->tester->amOnPage(Url::to([static::ROUTE_UPDATE, 'id' => $issueId]));
	}

	public function selectType(string $value): void {
		$this->tester->fillOutSelect2OptionField('.field-issueform-type_id', $value);
		$this->tester->wait(1);
	}

	public function selectStage(string $value): void {
		$this->tester->fillOutSelect2OptionField('.field-issueform-stage_id', $value);
	}

	public function selectEntityResponsible(string $value): void {
		$this->tester->fillOutSelect2OptionField('.field-issueform-entity_responsible_id', $value);
	}

	public function selectAgent(string $value): void {
		$this->tester->fillOutSelect2OptionField('.field-issueform-agent_id', $value);
	}

	public function selectLawyer(string $value): void {
		$this->tester->fillOutSelect2OptionField('.field-issueform-lawyer_id', $value);
	}

	public function fillSigningAt(string $value): void {
		$this->tester->fillField(static::FIELD_SIGNING_AT, $value);
	}

	public function fillStageChangeAt(string $value): void {
		$this->tester->fillField(static::FIELD_STAGE_CHANGE_AT, $value);
	}

	public function clickSubmitButton(): void {
		$this->tester->click('#issue-form button[type=submit]');
	}

}
