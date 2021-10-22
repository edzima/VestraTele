<?php

use common\models\message\IssuePayPayedMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyCustomer(),
			[1]
		),
	],
	[
		'id' => 2,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyCustomer(),
			[2]
		),
	],
	[
		'id' => 3,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_EMAIL,
			IssuePayPayedMessagesForm::keyCustomer(),
			[1, 2]
		),
	],
	[
		'id' => 4,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_SMS,
			IssuePayPayedMessagesForm::keyWorkers(),
			[1]
		),
	],
	[
		'id' => 5,
		'key' => IssuePayPayedMessagesForm::generateKey(
			IssuePayPayedMessagesForm::TYPE_EMAIL,
			IssuePayPayedMessagesForm::keyWorkers(),
		),
	],
];
