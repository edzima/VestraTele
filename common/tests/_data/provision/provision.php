<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	'nowak-self-unpaid' => [
		'pay_id' => 1,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 500,
	],
	'nowak-self-unpaid-hidden' => [
		'pay_id' => 1,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 100,
		'hide_on_report' => true,
	],

	'nowak-self-paid' => [
		'pay_id' => 2,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 500,
	],
	'nowak-self-paid-hidden' => [
		'pay_id' => 2,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 1,
		'value' => 100,
		'hide_on_report' => true,
	],
	'nowak-self-paid-administrative' => [
		'pay_id' => 3,
		'from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'type_id' => 3,
		'value' => 100,
	],
];
