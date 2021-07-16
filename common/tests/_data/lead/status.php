<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'id' => LeadStatusInterface::STATUS_NEW,
		'name' => 'New',
	],
	[
		'id' => LeadStatusInterface::STATUS_ARCHIVE,
		'name' => 'Archive',
	],
	[
		'name' => 'Not Answered',
		'short_report' => true,
	],
];
