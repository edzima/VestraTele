<?php

use common\models\issue\IssuePayCalculation;

return [
	'not-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => 300,
		'stage_id' => 1,
	],
	'payed' => [
		'issue_id' => 2,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => 300,
		'stage_id' => 1,
	],
	'many-pays' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => 300,
		'stage_id' => 2,
	],
	'with-problem-status' => [
		'issue_id' => 3,
		'value' => 2460,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND,
		'provider_id' => 200,
		'owner_id' => 301,
		'stage_id' => 1,
	],
	'with-problem-status_and_pay' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_DEMAND,
		'provider_id' => 200,
		'owner_id' => 303,
		'stage_id' => 2
	],
	'archived-issue' => [
		'issue_id' => 6,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_DEMAND,
		'provider_id' => 200,
		'owner_id' => 303,
		'stage_id' => 2
	],
];
