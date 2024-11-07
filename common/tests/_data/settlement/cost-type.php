<?php

use common\fixtures\helpers\SettlementFixtureHelper;

return [
	'installment-active' => [
		'id' => SettlementFixtureHelper::COST_TYPE_ID_INSTALLMENT,
		'name' => 'Installment',
		'is_active' => true,
		'is_for_settlement' => true,
		'options' => [
			'user_is_required' => true,
		],
	],
	'not-active' => [
		'id' => SettlementFixtureHelper::COST_TYPE_ID_NOT_ACTIVE,
		'name' => 'Administrative',
		'is_active' => false,
		'is_for_settlement' => true,
	],
	'office' => [
		'id' => SettlementFixtureHelper::COST_TYPE_ID_OFFICE,
		'name' => 'Office',
		'is_active' => true,
	],
	'purchase-receivables' => [
		'id' => SettlementFixtureHelper::COST_TYPE_PURCHASE_OF_RECEIVABLES,
		'name' => 'Purchase of receivables',
		'is_active' => true,
	],
	'justification-of-the-judgment' => [
		'id' => SettlementFixtureHelper::COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'name' => 'Justification of the judgment',
		'is_active' => true,
	],
];
