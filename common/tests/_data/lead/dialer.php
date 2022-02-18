<?php

use common\modules\lead\models\LeadDialer;

return [
	[
		'lead_id' => 1,
		'type_id' => 1,
		'priority' => LeadDialer::PRIORITY_HIGH,
	],
	[
		'lead_id' => 2,
		'type_id' => 1,
		'priority' => LeadDialer::PRIORITY_LOW,
	],
	[
		'lead_id' => 2,
		'type_id' => 2,
		'priority' => LeadDialer::PRIORITY_MEDIUM,
	],
];
