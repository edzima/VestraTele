<?php

use common\modules\reminder\models\Reminder;

return [
	'low' => [
		'priority' => Reminder::PRIORITY_LOW,
		'date_at' => '2020-01-01',
	],
	'high' => [
		'priority' => Reminder::PRIORITY_HIGH,
		'date_at' => '2020-01-01',
	],
];
