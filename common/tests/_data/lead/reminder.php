<?php

use common\modules\reminder\models\Reminder;

return [
	'low' => [
		'priority' => Reminder::PRIORITY_LOW,
		'details' => 'Reminder with low priority.',
	],
	'medium' => [
		'priority' => Reminder::PRIORITY_MEDIUM,
		'details' => 'Reminder with medium priority.',
	],
	'high' => [
		'priority' => Reminder::PRIORITY_MEDIUM,
		'details' => 'Reminder with high priority.',
	],
];
