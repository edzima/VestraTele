<?php

use common\models\message\IssueStageChangeMessagesForm;

return [
	[
		'id' => 1,
		'key' => IssueStageChangeMessagesForm::generateKey(
			IssueStageChangeMessagesForm::TYPE_EMAIL,
			IssueStageChangeMessagesForm::keyWorkers(),
		),
	],
	[
		'id' => 2,
		'key' => IssueStageChangeMessagesForm::generateKey(
			IssueStageChangeMessagesForm::TYPE_SMS,
			IssueStageChangeMessagesForm::keyCustomer(),
			[],
			1,
			7
		),
	],
];
