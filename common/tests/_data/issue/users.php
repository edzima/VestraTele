<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueUser;

return [
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
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
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 201,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 301,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => 401,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 201,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 300,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 201,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => 300,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 301,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 201,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => 300,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 301,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => 202,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => 300,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => 301,
		'issue_id' => 6,
	],
];
