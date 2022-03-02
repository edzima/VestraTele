<?php

use common\modules\lead\entities\Dialer;
use common\modules\lead\models\LeadDialer;

return [
	[
		'lead_id' => 1,
		'type_id' => 1,
		'priority' => LeadDialer::PRIORITY_MEDIUM,
		'status' => Dialer::STATUS_ESTABLISH,
	],
	[
		'lead_id' => 5,
		'type_id' => 1,
		'priority' => LeadDialer::PRIORITY_HIGH,
		'status' => Dialer::STATUS_NEW,
	],
	[
		'lead_id' => 6,
		'type_id' => 3,
		'priority' => LeadDialer::PRIORITY_HIGH,
		'status' => Dialer::STATUS_NEW,
		'last_at' => strtotime('2020-01-01'),
	],
	[
		'lead_id' => 2,
		'type_id' => 2,
		'priority' => LeadDialer::PRIORITY_MEDIUM,
		'status' => Dialer::STATUS_NEW,
	],
	[
		'lead_id' => 3,
		'type_id' => 1,
		'priority' => LeadDialer::PRIORITY_MEDIUM,
		'status' => Dialer::STATUS_NEW,
	],
];
