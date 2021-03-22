<?php

use common\models\issue\IssuePayCalculation;

return [
	'administrative' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
	],
	'honorarium-single' => [
		'issue_id' => 2,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
	'honorarium-double' => [
		'issue_id' => 2,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
];
