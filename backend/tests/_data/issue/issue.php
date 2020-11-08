<?php

use backend\modules\issue\models\IssueStage;

return [
	[
		'type_id' => 1,
		'stage_id' => 1,
		'entity_responsible_id' => 1,
	],
	[
		'type_id' => 1,
		'stage_id' => 2,
		'entity_responsible_id' => 1,
	],
	[
		'type_id' => 2,
		'stage_id' => 2,
		'entity_responsible_id' => 1,
	],
	[
		'type_id' => 2,
		'stage_id' => 1,
		'entity_responsible_id' => 2,
	],
	'archived' =>[
		'type_id' => 1,
		'stage_id' => IssueStage::ARCHIVES_ID,
		'entity_responsible_id' => 1,
		'archives_nr' => 'A1000',
	],

];
