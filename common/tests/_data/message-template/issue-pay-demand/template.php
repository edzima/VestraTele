<?php

use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\message\IssuePayDelayedMessagesForm;
use console\models\DemandForPayment;

return [
	[
		'id' => 1,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 2,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[2, 3],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 3,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_EMAIL,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1, 2],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 4,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_EMAIL,
			IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
	[
		'id' => 5,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1],
			SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		),
	],
];
