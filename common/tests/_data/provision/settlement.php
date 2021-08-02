<?php

use common\models\issue\IssueSettlement;

return [
	'accident-honorarium-single-pay-not-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssueSettlement::TYPE_HONORARIUM,
	],
	'accident-honorarium-single-pay-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssueSettlement::TYPE_HONORARIUM,
	],
	'administrative' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssueSettlement::TYPE_ADMINISTRATIVE,
	],
	'lawyer' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssueSettlement::TYPE_LAWYER,
	],
];
