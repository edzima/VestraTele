<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'user_id' => 1,
		'trait_id' => UserFixtureHelper::TRAIT_BAILIFF,
	],
	[
		'user_id' => 1,
		'trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND,
	],
	[
		'user_id' => 2,
		'trait_id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND,
	],
];
