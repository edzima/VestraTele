<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'trait_id' => UserFixtureHelper::TRAIT_BAILIFF,
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'trait_id' => UserFixtureHelper::TRAIT_BAILIFF,
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'trait_id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE,
	],
];
