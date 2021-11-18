<?php

namespace common\tests\unit\message;

use common\models\message\IssueMessagesForm;

class KeyIssueMessagesFormTest extends BaseIssueMessagesFormTest {

	protected function messageTemplateFixtureDir(): string {
		return '';
	}

	public function keysProvider(): array {
		return [
			'Custom' => [
				IssueMessagesForm::generateKey('customType', 'customKey'),
				'customType.issue.customKey',
			],
			'Sms Customer Without Issue Types' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_SMS,
					IssueMessagesForm::keyCustomer(),
				),
				'sms.issue.customer',
			],
			'Email Customer Without Issue Types' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_EMAIL,
					IssueMessagesForm::keyCustomer(),
				),
				'email.issue.customer',
			],
			'Sms Workers Without Issue Types' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_SMS,
					IssueMessagesForm::keyWorkers(),
				),
				'sms.issue.workers',
			],
			'Email Workers Without Issue Types' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_EMAIL,
					IssueMessagesForm::keyWorkers(),
				),
				'email.issue.workers',
			],
			'SMS Customer With Custom Param' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_SMS,
					IssueMessagesForm::keyCustomer(['onUpdate']),
				),
				'sms.issue.customer.onUpdate',
			],
			'SMS Workers With Custom Param' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_SMS,
					IssueMessagesForm::keyWorkers(['onUpdate']),
				),
				'sms.issue.workers.onUpdate',
			],
			'Email Customer With Custom Param' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_EMAIL,
					IssueMessagesForm::keyCustomer(['onUpdate']),
				),
				'email.issue.customer.onUpdate',
			],
			'Email Workers With Custom Param' => [
				IssueMessagesForm::generateKey(
					IssueMessagesForm::TYPE_EMAIL,
					IssueMessagesForm::keyWorkers(['onUpdate']),
				),
				'email.issue.workers.onUpdate',
			],

		];
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
