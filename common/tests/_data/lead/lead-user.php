<?php

use common\modules\lead\models\LeadUser;

return [
	[
		'type' => LeadUser::TYPE_OWNER,
		'user_id' => 1,
		'lead_id' => 1,
	],
	[
		'type' => LeadUser::TYPE_DIALER,
		'user_id' => 1,
		'lead_id' => 1,
	],
	[
		'type' => LeadUser::TYPE_AGENT,
		'user_id' => 2,
		'lead_id' => 1,
	],
	[
		'type' => LeadUser::TYPE_OWNER,
		'user_id' => 2,
		'lead_id' => 2,
	],
	[
		'type' => LeadUser::TYPE_OWNER,
		'user_id' => 2,
		'lead_id' => 3,
	],
	[
		'type' => LeadUser::TYPE_MARKET_FIRST,
		'user_id' => 3,
		'lead_id' => 1,
	],
];
