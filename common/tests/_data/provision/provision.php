<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	'nowak-self-not-payed' => [
		'pay_id' => 1,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 500,
	],
	'nowak-self-payed' => [
		'pay_id' => 2,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 500,
	],
];
