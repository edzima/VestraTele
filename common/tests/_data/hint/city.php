<?php

use common\fixtures\helpers\TerytFixtureHelper;
use common\models\hint\HintCity;

return [
	'new-commission' => [
		'status' => HintCity::STATUS_NEW,
		'type' => HintCity::TYPE_COMMISSION_REFUNDS,
		'user_id' => 1,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
	],
	'new-care-benefits' => [
		'status' => HintCity::STATUS_NEW,
		'type' => HintCity::TYPE_CARE_BENEFITS,
		'user_id' => 1,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
	],
];
