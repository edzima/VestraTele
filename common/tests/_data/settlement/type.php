<?php

use common\fixtures\helpers\SettlementFixtureHelper;

return [
	[
		'id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		'name' => 'Honorarium',
		'is_active' => true,
	],
	[
		'id' => SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
		'name' => 'Administrative',
		'is_active' => true,
	],
	[
		'id' => SettlementFixtureHelper::TYPE_ID_LAWYER,
		'name' => 'Lawyer',
		'is_active' => true,
	],
	[
		'id' => SettlementFixtureHelper::TYPE_ID_NOT_ACTIVE,
		'name' => 'Not active',
		'is_active' => false,
	],
];
