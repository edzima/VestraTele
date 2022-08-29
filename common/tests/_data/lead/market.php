<?php

use common\modules\lead\models\LeadMarket;

return [
	[
		'lead_id' => 1,
		'status' => LeadMarket::STATUS_NEW,
		'creator_id' => 1,
	],
	[
		'lead_id' => 3,
		'status' => LeadMarket::STATUS_NEW,
		'creator_id' => 2,
	],
];
