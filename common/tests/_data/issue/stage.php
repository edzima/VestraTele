<?php

use common\models\issue\IssueStage;

return [
	[
		'name' => 'Completing documents',
		'short_name' => 'CD',
		'days_reminder' => 28,
	],
	[
		'name' => 'Proposal',
		'short_name' => 'P',
		'days_reminder' => 56,
	],
	[
		'name' => 'Stage with min 2 calculation',
		'short_name' => 'ST2',
	],
	[
		'id' => IssueStage::POSITIVE_DECISION_ID,
		'name' => 'Positive Decision',
		'short_name' => 'PD',
		'days_reminder' => 28,
	],
	[
		'id' => IssueStage::ARCHIVES_ID,
		'name' => 'Archives',
		'short_name' => 'A',
	],

];
