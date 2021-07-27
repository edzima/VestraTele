<?php

use common\models\issue\IssuePayCalculation;

return [
	'accident-honorarium-single-pay-not-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
	'accident-honorarium-single-pay-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
	],
	'administrative' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
	],
	'lawyer' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssuePayCalculation::TYPE_LAWYER,
	],
];
