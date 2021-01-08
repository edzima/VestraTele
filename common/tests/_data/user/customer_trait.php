<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\UserTrait;

return [
	[
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'trait_id' => UserTrait::TRAIT_BAILIFF,
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'trait_id' => UserTrait::TRAIT_BAILIFF,
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'trait_id' => UserTrait::TRAIT_DISABILITY_RESULT_OF_CASE,
	],
];
