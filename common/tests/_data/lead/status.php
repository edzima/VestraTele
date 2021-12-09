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
	],
	[
		'name' => 'Calling',
		'short_report' => true,
	],
	[
		'name' => 'Not Answered',
		'short_report' => true,
	],
	[
		'name' => 'Answered',
		'short_report' => true,
	],

];
