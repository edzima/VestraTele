<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'id' => UserFixtureHelper::TRAIT_ANTYVINDICATION,
		'name' => 'Antyvindication',
	],
	[
		'id' => UserFixtureHelper::TRAIT_BAILIFF,
		'name' => 'Bailiff',
		'show_on_issue_view' => 1,
	],
	[
		'id' => UserFixtureHelper::TRAIT_COMMISSION_REFUND,
		'name' => 'Commission Refund',
	],
	[
		'id' => UserFixtureHelper::TRAIT_DISABILITY_RESULT_OF_CASE,
		'name' => 'Disability result of case',
	],
];
