<?php

namespace common\tests\unit\message;

use common\models\issue\IssueSettlement;
use common\models\message\IssuePayDelayedMessagesForm;
use console\models\DemandForPayment;

class IssuePayDelayedMessagesFormTest extends BaseIssueMessagesFormTest {

	public function keysProvider(): array {
		return [
			'SMS Customer First Demand for Honorarium Issue Type 1' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
					[1],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.delayed.customer.demandWhich:first.settlementType:30.issueTypes:1',
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
			'SMS Workers First Demand for Honorarium Issue Type 1' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
					[1],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.delayed.workers.demandWhich:first.settlementType:30.issueTypes:1',
			],
			'SMS Workers First Demand for Honorarium Issue Type 2 & 3' => [
				IssuePayDelayedMessagesForm::generateKey(
					IssuePayDelayedMessagesForm::TYPE_SMS,
					IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
					[2, 3],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.delayed.workers.demandWhich:first.settlementType:30.issueTypes:2,3',
			],
		];
	}
}
