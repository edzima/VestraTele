<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'owner_id' => 1,
		'lead_id' => 1,
		'old_status_id' => LeadStatusInterface::STATUS_NEW,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'details' => 'Some details',
		'created_at' => '2020-01-01',
	],
	[
		'owner_id' => 1,
		'lead_id' => 2,
		'old_status_id' => LeadStatusInterface::STATUS_NEW,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'created_at' => '2020-01-01',
	],
	[
		'owner_id' => 2,
		'lead_id' => 3,
		'old_status_id' => LeadStatusInterface::STATUS_NEW,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'created_at' => '2020-01-02',
	],
];
