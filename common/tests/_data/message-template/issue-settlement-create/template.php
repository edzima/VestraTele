<?php

use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\message\IssueSettlementCreateMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM
		),
	],
	[
		'id' => 2,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[2],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM

		),
	],
	[
		'id' => 3,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyCustomer(),
			[1, 2],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM

		),
	],
	[
		'id' => 4,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_SMS,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM
		),
	],
	[
		'id' => 5,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM
		),
	],
	[
		'id' => 6,
		'key' => IssueSettlementCreateMessagesForm::generateKey(
			IssueSettlementCreateMessagesForm::TYPE_EMAIL,
			IssueSettlementCreateMessagesForm::keyWorkers(),
			[],
			SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE
		),
	],
];
