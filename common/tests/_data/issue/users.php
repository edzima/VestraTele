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
		'user_id' => UserFixtureHelper::LAWYER_1,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => UserFixtureHelper::TELE_1,
		'issue_id' => 1,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => UserFixtureHelper::LAWYER_2,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => UserFixtureHelper::TELE_2,
		'issue_id' => 2,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => UserFixtureHelper::LAWYER_2,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'issue_id' => 3,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_LENNON,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => UserFixtureHelper::LAWYER_2,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => UserFixtureHelper::TELE_1,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'issue_id' => 4,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => UserFixtureHelper::LAWYER_2,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => UserFixtureHelper::TELE_1,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'issue_id' => 5,
	],
	[
		'type' => IssueUser::TYPE_CUSTOMER,
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_LAWYER,
		'user_id' => UserFixtureHelper::LAWYER_3_INACTIVE,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_TELEMARKETER,
		'user_id' => UserFixtureHelper::TELE_3_INACTIVE,
		'issue_id' => 6,
	],
	[
		'type' => IssueUser::TYPE_AGENT,
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'issue_id' => 6,
	],
];
