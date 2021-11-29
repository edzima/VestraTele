<?php

use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePayCalculation;

return [
	'not-payed-with-double-costs' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_JOHN,
		'stage_id' => 1,
	],
	'payed-with-single-costs' => [
		'issue_id' => 3,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_ADMINISTRATIVE,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_JOHN,
		'stage_id' => 1,
	],
	'many-pays-without-costs' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_JOHN,
		'stage_id' => 2,
	],
	'with-problem-status' => [
		'issue_id' => 3,
		'value' => 2460,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_NICOLE,
		'stage_id' => 1,
	],
	'with-problem-status_and_pay' => [
		'issue_id' => 1,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_DEMAND,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_NICOLE,
		'stage_id' => 2,
	],
	'archived-issue' => [
		'issue_id' => 6,
		'value' => 1230,
		'type' => IssuePayCalculation::TYPE_HONORARIUM,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'problem_status' => IssuePayCalculation::PROBLEM_STATUS_DEMAND,
		'provider_id' => 200,
		'owner_id' => SettlementFixtureHelper::OWNER_NICOLE,
		'stage_id' => 2,
	],
	'lawyer' => [
		'issue_id' => 1,
		'value' => 100,
		'type' => IssuePayCalculation::TYPE_LAWYER,
		'provider_type' => IssuePayCalculation::PROVIDER_CLIENT,
		'provider_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'owner_id' => SettlementFixtureHelper::OWNER_NICOLE,
		'stage_id' => 2,
	],
];
