<?php

use common\modules\lead\models\LeadStatusInterface;

return [
	'new-without-owner' => [
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'type_id' => 1,
		'date_at' => '2020-01-01',
	],
];
