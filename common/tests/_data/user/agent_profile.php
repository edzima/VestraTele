<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'firstname' => 'Peter',
		'lastname' => 'Nowak',
		'phone' => '+48 122 222 110',
	],
	[
		'user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'firstname' => 'Erika',
		'lastname' => 'Larson',
		'phone' => '+48 233 222 110',
	],
	[
		'user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
		'firstname' => 'Tommy',
		'lastname' => 'Set',
		'phone' => '+48 111 222 110',
	],
	[
		'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		'firstname' => 'Emily',
		'lastname' => 'Pat',
		'phone' => '+48 222 222 222',
	],
];
