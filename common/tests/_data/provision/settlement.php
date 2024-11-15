<?php

use common\fixtures\helpers\SettlementFixtureHelper;

return [
	'accident-honorarium-single-pay-not-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
	],
	'accident-honorarium-single-pay-payed' => [
		'issue_id' => 1,
		'value' => 1230,
		'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
	],
	'administrative' => [
		'issue_id' => 1,
		'value' => 100,
		'type_id' => SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
	],
	'lawyer' => [
		'issue_id' => 1,
		'value' => 100,
		'type_id' => SettlementFixtureHelper::TYPE_ID_LAWYER,
	],
	'honorarium-larson-customer' => [
		'issue_id' => 1,
		'value' => 1230,
		'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
	],
	'percentage-half' => [
		'issue_id' => 1,
		'value' => 0.5,
		'type_id' => SettlementFixtureHelper::TYPE_ID_PERCENTAGE,
	],
];
