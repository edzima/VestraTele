<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;
use common\models\message\IssuePayPayedMessagesForm;
use yii\swiftmailer\Message;

/**
 * @property IssuePayPayedMessagesForm $model
 */
class IssuePayPayedMessagesFormTest extends IssuePayMessagesFormTest {

	protected const MODEL_CLASS = IssuePayPayedMessagesForm::class;

	protected function messageTemplateFixtureDir(): string {
		return MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED;
	}

	public function testPayValue(): void {
		$this->giveModel();
		$email = $this->model->getEmailToCustomer();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString($this->getFormattedPayValue(false), $email);
		$email = $this->model->getEmailToWorkers();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString($this->getFormattedPayValue(false), $email);
	}

	public function testPartPaymentCustomerSms(): void {
		$this->giveModel();
		$this->model->isPartPayment = true;
		$smsPart = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($smsPart);

		$this->giveModel();
		$this->model->isPartPayment = false;
		$smsNotPart = $this->model->getSmsToCustomer();

		$this->tester->assertNotSame($smsPart->message, $smsNotPart->message);
	}

	public function keysProvider(): array {
		return [
			'Customer Email for Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyCustomer(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.customer.settlementType:30.issueTypes:1,2',
			],
			'Customer SMS for Part Payed Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_SMS,
					IssuePayPayedMessagesForm::keyCustomer([IssuePayPayedMessagesForm::KEY_PART_PAYMENT]),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.payed.customer.partPayment.settlementType:30.issueTypes:1,2',
			],
			'Customer Email for Honorarium Without Issue Types' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyCustomer(),
					[],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.customer.settlementType:30',
			],
			'Workers Email for Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyWorkers(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.workers.settlementType:30.issueTypes:1,2',
			],
			'Workers Email for Honorarium Without Issue Types' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyWorkers(),
					[],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.workers.settlementType:30',
			],
		];
	}

	public function testEmailToCustomer(): void {
		$this->giveModel();
		$this->model->sendSmsToCustomer = false;
		$this->tester->assertTrue((bool) $this->model->pushCustomerMessages());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->customer->email, $email->getTo()));
		$this->tester->assertSame('Email. About Payed Pay Issue(TYPE_1 and TYPE_2) for Customer.', $email->getSubject());

		$this->tester->assertMessageBodyContainsString(
			$this->pay->calculation->getFrontendUrl(),
			$email
		);
	}

	public function testEmailToWorkers(): void {
		$this->giveModel();
		$model = $this->model;
		$this->model->sendSmsToAgent = false;

		$model->workersTypes = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER];
		$this->tester->assertTrue((bool) $this->model->pushWorkersMessages());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		/**
		 * @var Message $email
		 */
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->agent->email, $email->getBcc()));
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->tele->email, $email->getBcc()));
		$this->tester->assertSame(
			'Email. Pay Payed: '
			. $this->getFormattedPayValue(false)
			. ' ' . $this->pay->calculation->getIssueName()
			. ' (All types) for Worker.',
			$email->getSubject()
		);
		$this->tester->assertMessageBodyContainsString(
			$this->pay->calculation->getFrontendUrl(),
			$email
		);
	}

}
