<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	'not-transfer-300' => [
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'date_at' => '2019-01-01',
		'pay_id' => 1,
	],
	'transfer-300' => [
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'date_at' => '2019-01-01',
		'transfer_at' => '2019-02-01',
		'pay_id' => 2,
	],
	'not-transfer-301' => [
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'date_at' => '2019-01-01',
		'pay_id' => 3,
	],
	'transfer-301' => [
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'date_at' => '2019-01-01',
		'transfer_at' => '2019-02-01',
		'pay_id' => 4,
	],
	'not-transfer-302' => [
		'user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
		'date_at' => '2019-01-01',
		'pay_id' => 5,
	],
	'not-transfer-302-2' => [
		'user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
		'date_at' => '2019-01-01',
		'pay_id' => 6,
	],
];
