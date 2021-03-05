<?php

use common\models\issue\IssuePayCalculation;

return [
	'administrative' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
	],
	'honorarium' => [
		'issue_id' => 2,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
	'lawyer' => [
		'issue_id' => 2,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_LAWYER,
	],
	'many-pays' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
	'without-telemarketer' => [
		'issue_id' => 3,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
];
