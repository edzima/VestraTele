<?php

use common\models\issue\IssueStage;
use common\models\issue\IssueType;

return [
	[
		'type_id' => IssueType::ACCIDENT_ID,
		'stage_id' => 1,
	],
	[
		'type_id' => IssueType::ACCIDENT_ID,
		'stage_id' => 2,
	],
	[
		'type_id' => IssueType::ACCIDENT_ID,
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
		'stage_id' => IssueStage::POSITIVE_DECISION_ID,
		'min_calculation_count' => 1,
	],
];
