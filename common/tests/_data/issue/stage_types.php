<?php

use common\models\issue\IssueStage;

return [
	[
		'type_id' => 1,
		'stage_id' => 1,
	],
	[
		'type_id' => 1,
		'stage_id' => 2,
	],
	[
		'type_id' => 1,
		'stage_id' => IssueStage::ARCHIVES_ID,
	],
	[
		'type_id' => 2,
		'stage_id' => 1,
	],
	[
		'type_id' => 2,
		'stage_id' => IssueStage::ARCHIVES_ID,
	],
	[
		'type_id' => 2,
		'stage_id' => 3,
		'min_calculation_count' => 1,
	],
	[
		'type_id' => 2,
		'stage_id' => 2,
		'min_calculation_count' => 2,
	],
	[
		'type_id' => 3,
		'stage_id' => 3,
	],
	[
		'type_id' => 1,
		'stage_id' => IssueStage::ARCHIVES_ID,
	],
	[
		'type_id' => 4,
		'stage_id' => 1,
	],
	[
		'type_id' => 4,
		'stage_id' => 2,
	],
	[
		'type_id' => 4,
		'stage_id' => IssueStage::ARCHIVES_ID,
	],
];
