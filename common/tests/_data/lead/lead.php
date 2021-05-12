<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	'new-wordpress-accident' => [
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
	'new-without-owner' => [
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
];
