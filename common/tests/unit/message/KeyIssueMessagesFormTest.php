<?php

namespace common\tests\unit\message;

use common\models\message\IssueMessagesForm;

class KeyIssueMessagesFormTest extends BaseIssueMessagesFormTest {

	public function testGenerateCustomKey(): void {
		$key = IssueMessagesForm::generateKey('sms', 'custom.key');
		$this->tester->assertSame('sms.issue.custom.key', $key);
	}

	public function testGenerateKeyWithoutIssueTypes(): void {
		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_SMS,
			IssueMessagesForm::keyCustomer(),
		);
		$this->tester->assertSame('sms.issue.customer', $key);
		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_EMAIL,
			IssueMessagesForm::keyCustomer(),
		);
		$this->tester->assertSame('email.issue.customer', $key);

		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_SMS,
			IssueMessagesForm::keyWorkers(),
		);
		$this->tester->assertSame('sms.issue.workers', $key);
		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_EMAIL,
			IssueMessagesForm::keyWorkers(),
		);
		$this->tester->assertSame('email.issue.workers', $key);
	}

	public function testGenerateKeyWithEmptyIssueType(): void {
		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_SMS,
			'onUpdate',
			[]
		);
		$this->tester->assertSame('sms.issue.onUpdate', $key);

		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_SMS,
			IssueMessagesForm::keyCustomer(),
			[]
		);
		$this->tester->assertSame('sms.issue.customer', $key);
	}

	public function testGenerateKeyWithParams(): void {
		$key = IssueMessagesForm::generateKey(
			IssueMessagesForm::TYPE_SMS,
			IssueMessagesForm::keyCustomer(['onUpdate']),
		);
		$this->tester->assertSame('sms.issue.customer.onUpdate', $key);
	}

	public function testCustomerSmsWithTemplateWithoutTypeIds(): void {
		$this->templateFixture->save(
			IssueMessagesForm::generateKey(
				IssueMessagesForm::TYPE_SMS,
				IssueMessagesForm::keyCustomer(),
			), 'SMS to Customer',
			'SMS Message to Customer'
		);

		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($sms);
	}

	public function testCustomerSmsWithTemplateWithOtherIssueTypeIds(): void {
		$this->templateFixture->flushAll();

		$this->templateFixture->save(
			IssueMessagesForm::generateKey(
				IssueMessagesForm::TYPE_SMS,
				IssueMessagesForm::keyCustomer(),
				[2]
			),
			'SMS to Customer',
			'SMS Message to Customer',
		);

		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNull($sms);
		$this->giveIssue(2);
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($sms);
	}

}
