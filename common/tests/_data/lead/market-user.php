<?php

use common\modules\lead\models\LeadMarketUser;

return [
	[
		'market_id' => 1,
		'user_id' => 1,
		'status' => LeadMarketUser::STATUS_TO_CONFIRM,
		'details' => 'Please give me this Lead!',
		'days_reservation' => 2,
	],
];
