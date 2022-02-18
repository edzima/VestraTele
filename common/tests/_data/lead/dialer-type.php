<?php

use common\modules\lead\models\LeadDialerType;

return [
	[
		'name' => 'Dialer Active',
		'status' => LeadDialerType::STATUS_ACTIVE,
	],
	[
		'name' => 'Dialer Inactive',
		'status' => LeadDialerType::STATUS_INACTIVE,
	],
	[
		'name' => 'Dialer Deleted',
		'status' => LeadDialerType::STATUS_DELETED,
	],
];
