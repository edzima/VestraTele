<?php

use common\models\issue\IssueSettlement;
use common\models\message\IssuePayDelayedMessagesForm;
use common\models\message\IssuePayPayedMessagesForm;
use console\models\DemandForPayment;

return [
	[
		'id' => 1,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 2,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[2, 3],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 3,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_EMAIL,
			IssuePayDelayedMessagesForm::keyCustomer([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1, 2],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 4,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_EMAIL,
			IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 5,
		'key' => IssuePayDelayedMessagesForm::generateKey(
			IssuePayDelayedMessagesForm::TYPE_SMS,
			IssuePayDelayedMessagesForm::keyWorkers([IssuePayDelayedMessagesForm::KEY_DEMAND_WHICH => DemandForPayment::WHICH_FIRST]),
			[1],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
];
