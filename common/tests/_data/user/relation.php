<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\UserRelation;

return [
	[
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'type' => UserRelation::TYPE_SUPERVISOR,
	],
	[
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
		'type' => UserRelation::TYPE_SUPERVISOR,
	],
];
