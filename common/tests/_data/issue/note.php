<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueNote;

return [
	[
		'title' => 'Note title 1',
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'issue_id' => 1,
		'publish_at' => '2020-01-01',
	],
	'stage-change' => [
		'title' => 'Proposal (previous: Completing documents)',
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'issue_id' => 1,
		'publish_at' => '2020-01-01',
		'type' => IssueNote::generateType(
			IssueNote::generateType(IssueNote::TYPE_STAGE_CHANGE, 2),
			1
		),
	],
];
