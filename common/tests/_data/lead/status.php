<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'id' => LeadStatusInterface::STATUS_ARCHIVE,
		'name' => 'Archive',
	],
	[
		'id' => LeadStatusInterface::STATUS_NEW,
		'name' => 'New',
		'calendar' => '{"color":"#cc0000"}',
	],
	[
		'name' => 'Calling',
		'short_report' => true,
	],
	[
		'name' => 'Not Answered',
		'short_report' => true,
		'calendar' => '{"color":"#ffe599"}',
	],
	[
		'name' => 'Answered',
		'short_report' => true,
	],
	[
		'name' => 'Calling Exceeded Limit',
	],
];
