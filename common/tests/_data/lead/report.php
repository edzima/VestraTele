<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'owner_id' => 1,
		'lead_id' => 1,
		'schema_id' => 1,
		'old_status_id' => LeadStatusInterface::STATUS_NEW,
		'status_id' => LeadStatusInterface::STATUS_NEW,
	],
];
