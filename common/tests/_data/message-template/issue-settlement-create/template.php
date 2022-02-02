<?php

use common\models\issue\IssueSettlement;
use common\models\message\IssueSettlementCreateMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[1],
			IssueSettlement::TYPE_HONORARIUM
		),
	],
	[
		'id' => 2,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[2],
			IssueSettlement::TYPE_HONORARIUM

		),
	],
	[
		'id' => 3,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[1, 2],
			IssueSettlement::TYPE_HONORARIUM

		),
	],
	[
		'id' => 4,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[1],
			IssueSettlement::TYPE_HONORARIUM
		),
	],
	[
		'id' => 5,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[],
			IssueSettlement::TYPE_HONORARIUM
		),
	],
	[
		'id' => 6,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[],
			IssueSettlement::TYPE_ADMINISTRATIVE
		),
	],
];
