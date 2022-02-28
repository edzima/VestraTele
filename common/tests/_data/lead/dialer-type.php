<?php

use common\modules\lead\models\LeadDialerType;

return [
	[
		'name' => 'Dialer Active',
		'status' => LeadDialerType::STATUS_ACTIVE,
		'user_id' => 1,
	],
	[
		'name' => 'Dialer Inactive',
		'status' => LeadDialerType::STATUS_INACTIVE,
		'user_id' => 1,
	],
	[
		'name' => 'Dialer Deleted',
		'status' => LeadDialerType::STATUS_DELETED,
		'user_id' => 1,
	],
];
