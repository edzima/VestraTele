<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'user_id' => 2,
		'trait_id' => UserFixtureHelper::TRAIT_BAILIFF,
	],
	[
		'user_id' => 3,
		'trait_id' => UserFixtureHelper::TRAIT_BAILIFF,
	],
	[
		'user_id' => 3,
		'trait_id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE,
	],
];
