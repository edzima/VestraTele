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
];
