<?php

use common\models\user\UserTrait;

return [
	[
		'user_id' => 2,
		'trait_id' => UserTrait::TRAIT_BAILIFF,
	],
	[
		'user_id' => 3,
		'trait_id' => UserTrait::TRAIT_BAILIFF,
	],
	[
		'user_id' => 3,
		'trait_id' => UserTrait::TRAIT_DISABILITY_RESULT_OF_CASE,
	],
];
