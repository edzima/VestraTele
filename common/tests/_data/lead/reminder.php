<?php

use common\modules\reminder\models\Reminder;

return [
	'low' => [
		'priority' => Reminder::PRIORITY_LOW,
		'details' => 'Reminder with low priority.',
		'date_at' => '2020-01-01',
	],
	'medium' => [
		'priority' => Reminder::PRIORITY_MEDIUM,
		'details' => 'Reminder with medium priority.',
		'date_at' => '2020-02-01',

	],
	'high' => [
		'priority' => Reminder::PRIORITY_HIGH,
		'details' => 'Reminder with high priority.',
		'date_at' => '2020-01-01',
	],
];
