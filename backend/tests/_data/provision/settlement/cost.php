<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueCost;

return [
	'office' => [
		'issue_id' => 1,
		'type' => IssueCost::TYPE_OFFICE,
		'value' => 130,
		'vat' => 0,
		'date_at' => '2020-02-10',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	'settlement-installment' => [
		'issue_id' => 1,
		'type' => IssueCost::TYPE_INSTALLMENT,
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'value' => 100,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	'installment-not-settlement-same-issue' => [
		'issue_id' => 1,
		'type' => IssueCost::TYPE_INSTALLMENT,
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'value' => 100,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	'installment-not-settlement-other-issue' => [
		'issue_id' => 3,
		'type' => IssueCost::TYPE_INSTALLMENT,
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'value' => 100,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
];
