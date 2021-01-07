<?php

use common\models\user\UserTrait;

return [
	[
		'user_id' => 1,
		'trait_id' => UserTrait::TRAIT_BAILIFF,
	],
	[
		'user_id' => 1,
		'trait_id' => UserTrait::TRAIT_LIABILITIES,
	],
	[
		'user_id' => 2,
		'trait_id' => UserTrait::TRAIT_LIABILITIES,
	],
];
