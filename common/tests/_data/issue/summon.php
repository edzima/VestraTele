<?php

use common\models\issue\Summon;

return [
	'new' => [
		'status' => Summon::STATUS_NEW,
		'type' => Summon::TYPE_DOCUMENTS,
		'title' => 'Document summon',
		'issue_id' => 1,
		'owner_id' => 300,
		'contractor_id' => 300,
	],
];
