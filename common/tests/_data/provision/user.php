<?php

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;

return [
	'nowak-self-default' => [
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => ProvisionFixtureHelper::TYPE_AGENT_PERCENT_25,
		'value' => 25,
	],
	'nowak-to-miller' => [
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'type_id' => ProvisionFixtureHelper::TYPE_AGENT_PERCENT_25,
		'value' => 25,
	],
	'miller-self-not-default' => [
		'from_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'to_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'type_id' => ProvisionFixtureHelper::TYPE_AGENT_PERCENT_25,
		'value' => 30,
	],
];
