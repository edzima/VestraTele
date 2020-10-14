<?php

use common\models\issue\IssueUser;

return [
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => 100,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 200,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 300,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => 400,
		'issue_id' => 1,
	],

];
