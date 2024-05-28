<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'name' => 'png-file',
		'hash' => md5(microtime(true) . 'png-file'),
		'size' => 1024,
		'owner_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'type' => 'png',
		'mime' => 'image/png',
		'file_type_id' => 1,
	],
];
