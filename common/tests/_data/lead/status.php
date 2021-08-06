<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'id' => LeadStatusInterface::STATUS_NEW,
		'name' => 'New',
		'calendar' => '{"color":"#cc0000"}',
	],
	[
		'id' => LeadStatusInterface::STATUS_ARCHIVE,
		'name' => 'Archive',
	],
	[
		'name' => 'Not Answered',
		'short_report' => true,
		'calendar' => '{"color":"#ffe599"}',
	],
];
