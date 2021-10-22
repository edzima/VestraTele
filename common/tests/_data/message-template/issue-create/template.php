<?php

use common\models\message\IssueCreateMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssueCreateMessagesForm::generateKey(
			IssueCreateMessagesForm::TYPE_SMS,
			IssueCreateMessagesForm::keyCustomer(),
			[1]
		),
	],
	[
		'id' => 2,
		'key' => IssueCreateMessagesForm::generateKey(
			IssueCreateMessagesForm::TYPE_SMS,
			IssueCreateMessagesForm::keyCustomer(),
			[2]
		),
	],
	[
		'id' => 3,
		'key' => IssueCreateMessagesForm::generateKey(
			IssueCreateMessagesForm::TYPE_EMAIL,
			IssueCreateMessagesForm::keyCustomer(),
			[1, 2]
		),
	],
	[
		'id' => 4,
		'key' => IssueCreateMessagesForm::generateKey(
			IssueCreateMessagesForm::TYPE_SMS,
			IssueCreateMessagesForm::keyWorkers(),
			[1]
		),
	],
	[
		'id' => 5,
		'key' => IssueCreateMessagesForm::generateKey(
			IssueCreateMessagesForm::TYPE_EMAIL,
			IssueCreateMessagesForm::keyWorkers(),
		),
	],
];
