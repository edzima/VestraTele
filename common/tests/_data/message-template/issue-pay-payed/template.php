<?php

use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\message\IssuePayPayedMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyCustomer(),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 2,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyCustomer(),
			[2],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 3,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_EMAIL,
			IssuePayPayedMessagesForm::keyCustomer(),
			[1, 2],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 4,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyWorkers(),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 5,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_EMAIL,
			IssuePayPayedMessagesForm::keyWorkers(),
			[],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 6,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyCustomer([IssuePayPayedMessagesForm::KEY_PART_PAYMENT]),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
];
