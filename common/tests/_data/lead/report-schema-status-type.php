<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'schema_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'type_id' => 1,
	],
	[
		'schema_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'type_id' => 2,
	],
	[
		'schema_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'type_id' => 1,
	],
	[
		'schema_id' => 3,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'type_id' => 1,
	],
];
