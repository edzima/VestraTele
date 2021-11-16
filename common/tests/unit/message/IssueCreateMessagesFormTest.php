<?php

namespace common\tests\unit\message;

use common\components\message\MessageTemplateKeyHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueUser;
use common\models\message\IssueCreateMessagesForm;

class IssueCreateMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssueCreateMessagesForm::class;

	protected const MESSAGE_TEMPLATE_FIXTURE_DIR = MessageTemplateFixtureHelper::DIR_ISSUE_CREATE;

	public function keysProvider(): array {
		return [
			'SMS Customer Without Issue Types' => [
				IssueCreateMessagesForm::generateKey(
					IssueCreateMessagesForm::TYPE_SMS,
					IssueCreateMessagesForm::keyCustomer(),
				),
				'sms.issue.create.customer',
			],
			'SMS Customer With Issue Types' => [
				IssueCreateMessagesForm::generateKey(
					IssueCreateMessagesForm::TYPE_SMS,
					IssueCreateMessagesForm::keyCustomer(),
					[1, 2]
				),
				'sms.issue.create.customer.' . MessageTemplateKeyHelper::issueTypesKeyPart([1, 2]),
			],
		];
	}

	public function testCustomerSmsWithIssueType1(): void {
		$this->giveIssue(1);
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertSame('Sms About Create Issue(TYPE_1) for Customer.', $sms->note_title);
	}

	public function testCustomerSmsWithIssueType2(): void {
		$this->giveIssue(2);
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertSame('Sms About Create Issue(TYPE_2) for Customer.', $sms->note_title);
	}

	public function testCustomerSmsWithIssueType3(): void {
		$this->giveIssue(3);
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNull($sms);
	}

	public function testAgentSmsWithIssueType1(): void {
		$this->giveIssue(1);
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertSame('Sms. New Issue (TYPE_1) for Worker.', $sms->note_title);
	}

	public function testAgentSmsWithIssueType2(): void {
		$this->giveIssue(2);
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNull($sms);
	}

	public function testAgentEmail(): void {
		$this->giveIssue(1);
		$this->giveModel();
		$this->model->workersTypes = [IssueUser::TYPE_AGENT];
		$email = $this->model->getEmailToWorkers();
		$this->tester->assertSame('Email. New Issue ' . $this->issue->getIssueName() . ' (All types) for Worker.', $email->getSubject());
		$this->tester->assertStringContainsString($this->issue->getIssueModel()->customer->getFullName(), $email->toString());
	}

}
