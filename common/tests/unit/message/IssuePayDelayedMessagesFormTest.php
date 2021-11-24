<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueSettlement;
use common\models\message\IssuePayDelayedMessagesForm;
use console\models\DemandForPayment;

class IssuePayDelayedMessagesFormTest extends IssuePayMessagesFormTest {

	protected const MODEL_CLASS = IssuePayDelayedMessagesForm::class;
	private const DATE_FORMAT = 'Y-m-d';

	protected function messageTemplateFixtureDir(): string {
		return MessageTemplateFixtureHelper::DIR_ISSUE_PAY_DEMAND;
	}

	public function keysProvider(): array {
		return [
			'SMS Customer First Demand Issue Type 1' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
					[1]
				),
				'sms.issue.settlement.pay.delayed.customer.demandWhich:first.issueTypes:1',
			],
			'SMS Customer First Demand for Honorarium Issue Type 2 & 3' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
					[2, 3],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.delayed.customer.demandWhich:first.settlementType:30.issueTypes:2,3',
			],
			'SMS Workers Demand' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyWorkers(),
				),
				'sms.issue.settlement.pay.delayed.workers',
			],
		];
	}

	protected function getModelDefaultConfig(): array {
		$config = parent::getModelDefaultConfig();
		$config['whichDemand'] = DemandForPayment::WHICH_FIRST;
		$config['dateFormat'] = static::DATE_FORMAT;
		return $config;
	}

	public function testSmsCustomer(): void {
		$this->givePay(['deadline_at' => '2020-02-02']);
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($sms);
		$this->tester->assertStringContainsString($this->issue->getIssueModel()->agent->getFullName(), $sms->message);
		$this->tester->assertStringNotContainsString($this->issue->getIssueModel()->customer->getFullName(), $sms->message);
		$this->tester->assertStringContainsString($this->issue->getIssueType()->name, $sms->message);
		$this->tester->assertStringContainsString($this->getFormattedPayValue(true), $sms->message);
		$this->tester->assertStringContainsString($this->pay->getDeadlineAt()->format(static::DATE_FORMAT), $sms->message);
	}

	public function testSmsToAgent(): void {
		$this->givePay(['deadline_at' => date(static::DATE_FORMAT, strtotime('- 3 days'))]);
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNotNull($sms);
		$this->tester->assertStringContainsString($this->issue->getIssueModel()->customer->getFullName(), $sms->message);
		$this->tester->assertStringNotContainsString($this->issue->getIssueModel()->agent->getFullName(), $sms->message);
		$this->tester->assertStringContainsString('since 3 days', $sms->message);
	}

}
